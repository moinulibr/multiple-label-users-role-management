<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use App\Models\UserLoginPlatform;
use App\Models\UserProfile;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\UserContextManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; // For password verification in a real app
use Illuminate\Support\Facades\Log; // Changed to Log for better practice

class DynamicLoginController extends Controller
{
    protected OtpService $otpService;
    protected UserContextManager $userContextManager;

    // Inject OtpService via constructor
    public function __construct(OtpService $otpService, UserContextManager $userContextManager)
    {
        $this->otpService = $otpService;
        $this->userContextManager = $userContextManager;
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

        $user = User::where('phone', $normalizedPhone)->where('status',true)->first();
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
     * Checks if the user's role is allowed on the provided platform keys.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function identifyUser(Request $request)
    {
        $request->validate([
            'login_key' => 'required|string|min:5',
            'platform_hash_key' => 'required|string',
        ]);

        $loginKey = $request->login_key;
        $platformKey = $request->platform_hash_key;

        // 1. Platform Validation: Fetch the platform configuration
        $platform = UserLoginPlatform::where('platform_hash_key', $platformKey)
            ->where('status', true)
            ->first();

        if (!$platform) {
            return response()->json(['message' => 'Error: This login platform is unauthorized or disabled.', 'next_step' => 'error'], 403);
        }

        // The stored login_template_hash_key is an array of allowed user role hashes
        $allowedTemplateHashes = $platform->login_template_hash_key;


        // 1. Find the User
        if (filter_var($loginKey, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginKey)->where('status', true)->first();
            $loginKeyType = 'email';
        } else {
            $user = $this->findUserByNormalizedPhone($loginKey);
            $loginKeyType = 'phone';
        }

        if (!$user) {
            return response()->json(['message' => 'This account was not found in our system.'], 404);
        }

        $loginKeyValue = ($loginKeyType === 'phone' && $user) ? $user->phone : $loginKey;

        // 2. Find the User
        $business = Business::query()->where('owner_user_id', $user->id)->where('status', true);
        //Log::info("users business count- " . $business->count());
        //Log::info("users business get- " . json_encode($business->get()));
        //Log::info("users business first- " . json_encode($business->first()));
       
        $defaultBusiness = NULL; 
        if($business->count() && $business->count() == 1){
            $defaultBusiness = $business->first();
        }
        else if($business->count() && $business->count() > 1){
            $defaultBusiness = $business->where('default_login', true)->first();
        }else{
            $defaultBusiness = NULL;
        }
        
        $defaultProfile = NULL;
        $userProfile = UserProfile::query()->where('user_id', $user->id)->where('status',true);
        if($defaultBusiness){
            $defaultProfile = $userProfile->where('business_id', $defaultBusiness->id)
                ->where('default_login', true)
                ->first();
        }else{
            $defaultProfile = $userProfile->where('default_login', true)->first();
        }
        if(!$defaultProfile){
            return response()->json(['message' => 'Error: No associated active profile found for this user.', 'next_step' => 'error'], 403);
        }
        //Log::info("users user type- " . json_encode($defaultProfile->userType->login_template_hash_key));
        $userTypeLoginTemplateHashKey = $defaultProfile->userType->login_template_hash_key;

        //Log::info("allowedTemplateHashes- " . json_encode($allowedTemplateHashes));
        //Log::info("defaultProfile->userType->display_name- " . json_encode($defaultProfile->userType->display_name));

        // The core validation logic: Check if the user's role hash exists in the platform's allowed hashes array
        if (!in_array($userTypeLoginTemplateHashKey, $allowedTemplateHashes)) {
            return response()->json(['message' => "Error: Your profile ({$defaultProfile->userType->display_name}) is not authorized for this platform.", 'next_step' => 'error'], 403);
        }
        
        $selectedProfile = [
            'business_id' => $defaultBusiness ? $defaultBusiness->id : NULL,
            'profile' => $defaultProfile->id,
            'role' => $defaultProfile->userType->display_name,
        ];
        return $this->processVerificationStep($user, $loginKeyValue, $loginKeyType, $selectedProfile);

    }


    /**
     * Internal helper to handle OTP generation and preparing for the Verification step.
     * This is where the core OTP/Password decision is made.
     */
    protected function processVerificationStep(User $user, string $loginKeyValue, string $loginKeyType, array $selectedProfile)
    {
        // 1. Determine verification method
        $step2Method = ($loginKeyType === 'phone') ? 'otp' : 'password';
        $message = 'Please enter your password.';

        // 2. Handle OTP generation (for phone login)
        if ($step2Method === 'otp') {
            try {
                // Use the database's phone value as recipient
                // NOTE: Using $user->phone assumes the phone is stored correctly in the DB
                $otpCode = $this->otpService->generateAndSaveOtp($user, $user->phone, 'login', 5);
                $message = "OTP successfully generated and sent to your phone. (OTP: $otpCode - For testing purposes)";
            } catch (\Exception $e) {
                Log::error("OTP Creation Failed: " . $e->getMessage());
                return response()->json(['message' => 'Failed to generate OTP. Check server logs.'], 500);
            }
        }

        // 3. Return parameters for the next view/state
        return response()->json([
            'message' => $message,
            'next_step' => 'verification',
            'login_key_value' => $loginKeyValue,
            'login_key_type' => $loginKeyType,
            'step_2_method' => $step2Method,
            'business_id' => $selectedProfile['business_id'],
            'profile' => $selectedProfile['profile'],
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
            'business_id' => 'nullable',
            'profile' => 'required',
            'role' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Failed.', 'errors' => $validator->errors()], 422);
        }

        $loginKey = $request->input('login_key');
        $inputCredential = $request->input('credential');
        $step2Method = $request->input('step_2_method');
        $businessId = $request->input('business_id');
        $role = $request->input('role');
        $profile_id = $request->input('profile');

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
            // NOTE: Use $user->password field for Hash::check. Assuming User model has a 'password' field.
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

            // Verify OTP using the service (using $user->phone which should be the database value)
            $otpAttempt = $this->otpService->verifyOtp($user->phone, $inputCredential, 'login');

            if ($otpAttempt) {
                Auth::login($user);
                // Mark OTP as used
                $this->otpService->markOtpAsUsed($otpAttempt);
                $isLoggedIn = true;
            }
        }

        $userProfile = UserProfile::find($profile_id);

        // Final login result
        if ($isLoggedIn) {
            $this->userContextManager->setContext($userProfile, $user);
            //$request->session()->regenerate();
            // Store the selected profile context in session
            //$request->session()->put('current_business_id', $businessId);
            //$request->session()->put('current_role', $role);

            // NOTE: Replace 'dashboard' with your actual route name
            $redirectUrl = '/dashboard';
            return response()->json([
                //'message' => "Successfully logged in as '{$role}' at Business ID '{$businessId}'. Redirecting...",
                'message' => "Successfully logged in as. Redirecting...",
                'redirect_url' => $redirectUrl,
            ], 200);
        }

        // Login failed
        $errorMsg = ($step2Method === 'password') ? 'The provided password is incorrect.' : 'The provided OTP is incorrect or expired.';
        return response()->json(['message' => $errorMsg], 401);
    }

    public function resendOtp(Request $request){
        $request->validate([
            'login_key' => 'required|string', // The phone number used
            'login_key_type' => ['required', 'string', 'in:phone'],
            'business_id' => 'nullable',
            'role' => 'nullable|string',
        ]);
        
        $loginKeyValue = $request->login_key;

        // Since we are resending OTP, we assume it's always a phone login
        $user = $this->findUserByNormalizedPhone($loginKeyValue);

        if (!$user) {
            return response()->json(['message' => 'User not found for OTP resend.'], 404);
        }

        // Use the same logic as the initial OTP generation
        $profileData = [
            'business_id' => $request->business_id,
            'role' => $request->role,
        ];

        // This will generate a new OTP and return the response data
        return $this->processVerificationStep(
            $user,
            $user->phone, // Use DB phone for consistency
            'phone',
            $profileData
        );
    }
}
