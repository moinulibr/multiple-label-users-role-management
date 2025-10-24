<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DynamicLoginController extends Controller
{
    protected OtpService $otpService;

    // Inject OtpService via constructor
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }


    /**
     * Mock function to get user profiles (businesses and roles) and group them by business.
     *
     * @param User $user
     * @return array
     */
    protected function getProfilesForUser(User $user)
    {
        $allProfiles = [];

        // Example mock logic: Assume user ID 1 and 2 have multiple profiles, others are general
        if ($user->id == 1) {
            $allProfiles = [
                ['business_id' => 101, 'business_name' => 'Alpha Corp', 'role' => 'Administrator'],
                ['business_id' => 102, 'business_name' => 'Beta Solutions', 'role' => 'Manager'],
                ['business_id' => 101, 'business_name' => 'Alpha Corp', 'role' => 'Employee'],
                ['business_id' => 103, 'business_name' => 'Gamma Tech', 'role' => 'Manager'],
            ];
        } elseif ($user->id == 2) {
            $allProfiles = [
                ['business_id' => 201, 'business_name' => 'Global Sales', 'role' => 'Sales'],
            ];
        } else {
            // Default General/Single profile for all other users
            $allProfiles = [
                ['business_id' => 999, 'business_name' => 'General Account', 'role' => 'General'],
            ];
        }

        // Group profiles by business
        $groupedProfiles = [];
        foreach ($allProfiles as $profile) {
            $businessKey = $profile['business_id'];
            if (!isset($groupedProfiles[$businessKey])) {
                $groupedProfiles[$businessKey] = [
                    'business_id' => $profile['business_id'],
                    'business_name' => $profile['business_name'],
                    'roles' => [],
                ];
            }
            // Only add the role details to the roles array
            $groupedProfiles[$businessKey]['roles'][] = $profile['role'];
        }

        // Return as a numerically indexed array
        return array_values($groupedProfiles);
    }

    protected function normalizePhone($phone)
    {
        $phone = preg_replace('/[\s-]+/', '', $phone);
        if (str_starts_with($phone, '+880')) {
            $phone = substr($phone, 1);
        }
        if (str_starts_with($phone, '880')) {
            return $phone;
        }
        if (str_starts_with($phone, '01')) {
            return '88' . $phone;
        }
        if (str_starts_with($phone, '1')) {
            return '880' . $phone;
        }
        return $phone;
    }

    /**
     * Finds the user by normalizing the phone number and searching by full/last 10 digits.
     * @param string $phone
     * @return User|null
     */
    protected function findUserByNormalizedPhone($phone)
    {
        $normalizedPhone = $this->normalizePhone($phone);
        $lastTenDigits = substr($normalizedPhone, -10);

        $user = User::where('phone', $normalizedPhone)->first();
        if (!$user) {
            $user = User::where(function ($query) use ($lastTenDigits) {
                $query->where('phone', 'like', '%' . $lastTenDigits)
                    ->orWhere('phone', 'like', '0' . $lastTenDigits)
                    ->orWhere('phone', 'like', '880' . $lastTenDigits);
            })->first();
        }
        return $user;
    }

    public function showLoginForm()
    {
        return view('auth.login_dynamic');
    }

    /**
     * Step 1: Identify user by email or phone. Determines next step.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function identifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_key' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Failed.', 'errors' => $validator->errors()], 422);
        }

        $loginKey = trim($request->input('login_key'));
        $loginKeyType = 'email';

        // Check if email or phone
        if (filter_var($loginKey, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginKey)->first();
            $loginKeyValue = $loginKey;
        } else {
            $loginKeyType = 'phone';
            $user = $this->findUserByNormalizedPhone($loginKey);
            $loginKeyValue = $user ? $user->phone : $loginKey;
        }

        if (!$user) {
            return response()->json(['message' => 'This account was not found in our system.'], 404);
        }

        $groupedProfiles = $this->getProfilesForUser($user);
        $businessCount = count($groupedProfiles);

        // Determine the next step based on profile count
        if ($businessCount > 1 || (isset($groupedProfiles[0]) && count($groupedProfiles[0]['roles']) > 1)) {
            // Step 2: Profile Selection required if multiple businesses OR one business with multiple roles
            return response()->json([
                'message' => 'Multiple businesses/roles found. Please select your desired business and role.',
                'next_step' => 'profile_selection',
                'login_key_value' => $loginKeyValue,
                'login_key_type' => $loginKeyType,
                'user_id' => $user->id,
                'profiles' => $groupedProfiles,
            ], 200);
        } else {
            // Go directly to Step 3 (Verification) with the single profile selected by default
            $selectedBusiness = $groupedProfiles[0] ?? ['business_id' => null, 'roles' => ['General']];
            $selectedRole = $selectedBusiness['roles'][0] ?? 'General';

            $selectedProfile = [
                'business_id' => $selectedBusiness['business_id'],
                'role' => $selectedRole,
            ];

            return $this->processVerificationStep($user, $loginKeyValue, $loginKeyType, $selectedProfile);
        }
    }

    /**
     * Step 2: Handle profile selection (Business ID and Role) and determine if OTP/Password is needed.
     * This endpoint handles both the initial business selection and the final business+role selection.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'login_key_value' => 'required|string',
            'login_key_type' => ['required', 'string', 'in:email,phone'],
            'business_id' => 'required',
            'role' => 'nullable|string', // Role can be null if only business is selected initially
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Failed.', 'errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found for this selection.'], 404);
        }

        $businessId = $request->business_id;
        $role = $request->role; // This is the final selected role, if available

        // Check if role is missing (meaning the user only selected the business, which has multiple roles)
        if (empty($role)) {
            $groupedProfiles = $this->getProfilesForUser($user);

            $selectedBusinessData = collect($groupedProfiles)->firstWhere('business_id', $businessId);

            if ($selectedBusinessData && count($selectedBusinessData['roles']) > 1) {
                // Return to Blade to show role selection for this specific business
                return response()->json([
                    'message' => "Please select your role for '{$selectedBusinessData['business_name']}'.",
                    'next_step' => 'role_selection', // New step state for Blade
                    'business_name' => $selectedBusinessData['business_name'],
                    'business_id' => $businessId,
                    'roles' => $selectedBusinessData['roles'],
                ], 200);
            }

            // If role is still null but only one role exists, auto-select it and proceed to verification
            if ($selectedBusinessData && count($selectedBusinessData['roles']) === 1) {
                $role = $selectedBusinessData['roles'][0];
                // Fall through to verification step
            } else {
                return response()->json(['message' => 'Invalid profile selection. Role is missing.'], 400);
            }
        }

        // Final profile selection complete, proceed to verification
        $selectedProfile = [
            'business_id' => $businessId,
            'role' => $role,
        ];

        return $this->processVerificationStep(
            $user,
            $request->login_key_value,
            $request->login_key_type,
            $selectedProfile
        );
    }

    /**
     * Internal helper to handle OTP generation and preparing for the Verification step.
     */
    protected function processVerificationStep(User $user, string $loginKeyValue, string $loginKeyType, array $selectedProfile)
    {
        $step2Method = ($loginKeyType === 'phone') ? 'otp' : 'password';
        $message = 'Please enter your password.';

        if ($step2Method === 'otp') {
            try {
                // Use the database's phone value as recipient
                $otpCode = $this->otpService->generateAndSaveOtp($user, $user->phone, 'login', 5);
                $message = "OTP successfully generated and sent to your phone. (OTP: $otpCode - For testing purposes)";
            } catch (\Exception $e) {
                \Log::error("OTP Creation Failed: " . $e->getMessage());
                return response()->json(['message' => 'Failed to generate OTP. Check server logs.'], 500);
            }
        }

        return response()->json([
            'message' => $message,
            'next_step' => 'verification',
            'login_key_value' => $loginKeyValue,
            'login_key_type' => $loginKeyType,
            'step_2_method' => $step2Method,
            'business_id' => $selectedProfile['business_id'],
            'role' => $selectedProfile['role'],
        ], 200);
    }

    /**
     * Step 3: Verify password/OTP and finalize login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizeLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_key' => 'required|string',
            'credential' => 'required|string', // password or OTP
            'step_2_method' => ['required', 'string', 'in:password,otp'],
            'business_id' => 'required',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Failed.', 'errors' => $validator->errors()], 422);
        }

        $loginKey = $request->input('login_key');
        $inputCredential = $request->input('credential');
        $step2Method = $request->input('step_2_method');
        $businessId = $request->input('business_id');
        $role = $request->input('role');

        $loginKeyType = filter_var($loginKey, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Find user
        $user = ($loginKeyType === 'email')
            ? User::where('email', $loginKey)->first()
            : $this->findUserByNormalizedPhone($loginKey);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $isLoggedIn = false;

        // Password Login (for email)
        if ($step2Method === 'password') {
            if ($loginKeyType === 'email' && Auth::attempt(['email' => $loginKey, 'password' => $inputCredential])) {
                $isLoggedIn = true;
            } else if ($loginKeyType === 'phone') {
                return response()->json(['message' => 'Only OTP method is allowed for phone number login.'], 401);
            }
        }
        // OTP Login (for phone)
        elseif ($step2Method === 'otp') {
            if ($loginKeyType === 'email') {
                return response()->json(['message' => 'Only Password method is allowed for email login.'], 401);
            }

            // Verify OTP using the service
            $otpAttempt = $this->otpService->verifyOtp($user->phone, $inputCredential, 'login');

            if ($otpAttempt) {
                // OTP successful
                // We must log the user in AND set the selected context (business/role) in the session
                Auth::login($user);
                $this->otpService->markOtpAsUsed($otpAttempt);

                // Store the selected profile context in session
                $request->session()->put('current_business_id', $businessId);
                $request->session()->put('current_role', $role);

                $isLoggedIn = true;
            }
        }

        // Final login result
        if ($isLoggedIn) {
            $request->session()->regenerate();
            // Assuming 'dashboard' is the correct redirect route after successful login
            $redirectUrl = route('dashboard');

            return response()->json([
                'message' => "Successfully logged in as '{$role}' at Business ID '{$businessId}'. Redirecting...",
                'redirect_url' => $redirectUrl,
            ], 200);
        }

        // Login failed
        $errorMsg = ($step2Method === 'password') ? 'The provided password is incorrect.' : 'The provided OTP is incorrect or expired.';
        return response()->json(['message' => $errorMsg], 401);
    }
}
