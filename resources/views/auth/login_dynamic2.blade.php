<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Login - Refined</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta id="base-url" content="{{ url('/') }}">
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            background-color: #f8fafc; /* Lighter, cleaner background */
        }

        .form-container {
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }

        .form-container.active {
            display: block;
            opacity: 1;
        }

        .input-focus-style:focus {
            /* Focus style matching the blue/indigo theme */
            --tw-ring-color: #4f46e5; /* Indigo 600 */
            border-color: #4f46e5;
        }
        
        .loader-white-border {
             border-color: rgba(255, 255, 255, 0.5); 
             border-top-color: white; 
        }

        /* Improved responsiveness for smaller screens */
        @media (max-width: 640px) {
            .login-card {
                padding: 1.5rem; /* Reduced padding on small screens */
                margin: 0 1rem;
                width: 95%;
            }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-sm w-full space-y-8 p-8 sm:p-10 bg-white shadow-2xl rounded-2xl border border-gray-100 login-card">
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 sm:h-10 sm:w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9.25 10H7.5V17H9.75ZM15.75 17L15.25 10H13.5V17H15.75ZM20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20Z" />
                </svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900" id="login-heading">
                Sign in to your Account
            </h2>
            <p class="mt-2 text-sm text-gray-500" id="login-subheading">
                Enter your details to securely login.
            </p>
        </div>

        <div id="message-box" class="hidden p-3 rounded-lg text-sm" role="alert"></div>

        <form class="space-y-6" id="login-form" onsubmit="event.preventDefault(); handleFinalizeLogin()">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="login-key-type-input" name="login_key_type">
            <input type="hidden" id="step-2-method-input" name="step_2_method">
            <input type="hidden" id="user-id-input" name="user_id">
            <input type="hidden" id="business-id-input" name="business_id">
            <input type="hidden" id="role-input" name="role">
            <input type="hidden" id="login-key-value-input" name="login_key_value">
            <input type="hidden" id="login-platform-hash-key-input" name="platform_hash_key" value="5eaaf16a98fae359e253d21e6bccb2c2">

            <div id="step-1" class="form-container active">
                <label for="login-key" class="sr-only">Email or Phone Number</label>
                <div class="mt-1">
                    <input id="login-key" type="text" autocomplete="username" required
                        class="input-focus-style block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 sm:text-sm"
                        placeholder="Email or Phone Number"
                        oninput="checkLoginKey()">
                </div>
                <button type="button" id="next-button" onclick="handleIdentifyUser()"
                    class="w-full mt-6 flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition duration-150 ease-in-out"
                    disabled>
                    Continue
                    <span id="next-loading-spinner" class="hidden ml-2 w-5 h-5 border-2 loader-white-border rounded-full animate-spin"></span>
                </button>
            </div>

            <div id="step-3-verification" class="form-container space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label id="credential-label" for="credential" class="text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <button type="button" id="change-key-button"
                            class="text-xs text-indigo-500 hover:text-indigo-700 font-medium whitespace-nowrap"
                            onclick="goToStep1()">
                            Change/Retype?
                        </button>
                    </div>
                    <span id="login-key-display" class="block text-xs text-gray-500 mb-3"></span>

                    <input id="credential" type="password" autocomplete="current-password" required
                        name="credential"
                        class="input-focus-style block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 sm:text-sm"
                        placeholder="Enter your password or OTP"
                        oninput="checkCredentialInput()">
                </div>

                <div class="flex justify-between items-center pt-2">
                    <button type="button" id="resend-otp-button" class="hidden text-sm text-indigo-500 hover:text-indigo-700 disabled:text-gray-400 font-medium" 
                            onclick="handleResendOtp()" disabled>
                        Resend OTP <span id="resend-timer" class="ml-1"></span>
                    </button>
                    <a href="/forgot-password" class="text-sm text-gray-500 hover:text-indigo-600 font-medium ml-auto">
                        Forgot Password?
                    </a>
                </div>

                <div>
                    <button type="submit" id="login-button"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition duration-150 ease-in-out"
                        disabled>
                        Sign In
                        <span id="login-loading-spinner" class="hidden ml-2 w-5 h-5 border-2 loader-white-border rounded-full animate-spin"></span>
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center pt-4">
            <p class="text-sm text-gray-500">
                Don't have an account yet? 
                <a href="/register" class="font-medium text-indigo-600 hover:text-indigo-700">
                    Sign Up
                </a>
            </p>
        </div>
    </div>


    <script>
        // --- Element Declarations ---
        const loginPlatformKeyInput = document.getElementById('login-platform-hash-key-input');
        const loginKeyInput = document.getElementById('login-key');
        const credentialInput = document.getElementById('credential');
        const nextButton = document.getElementById('next-button');
        const loginButton = document.getElementById('login-button');
        const resendOtpButton = document.getElementById('resend-otp-button');
        const resendTimerSpan = document.getElementById('resend-timer');
        const messageBox = document.getElementById('message-box');
        const credentialLabel = document.getElementById('credential-label');
        const loginKeyDisplay = document.getElementById('login-key-display');
        const csrfToken = document.querySelector('input[name="_token"]').value;

        const BASE_URL = document.getElementById('base-url').content;

        // Hidden Inputs
        const loginKeyTypeInput = document.getElementById('login-key-type-input');
        const step2MethodInput = document.getElementById('step-2-method-input');
        const userIdInput = document.getElementById('user-id-input');
        const businessIdInput = document.getElementById('business-id-input');
        const roleInput = document.getElementById('role-input');
        const loginKeyValueInput = document.getElementById('login-key-value-input');

        // Dynamic States
        let currentStep = 1;
        let lastEnteredLoginKey = '';
        let resendTimer = null;

        // --- Utility Functions (unchanged logic) ---

        function showMessage(message, type = 'success') {
            messageBox.textContent = message;
            messageBox.classList.remove('hidden', 'border-red-400', 'bg-red-100', 'text-red-700', 'border-green-400', 'bg-green-100', 'text-green-700', 'border-yellow-400', 'bg-yellow-100', 'text-yellow-700');

            if (type === 'error') {
                messageBox.classList.add('border-red-400', 'bg-red-100', 'text-red-700');
            } else if (type === 'warning') {
                messageBox.classList.add('border-yellow-400', 'bg-yellow-100', 'text-yellow-700');
            } else {
                messageBox.classList.add('border-green-400', 'bg-green-100', 'text-green-700');
            }
            messageBox.classList.remove('hidden');
        }

        function clearMessage() {
            messageBox.classList.add('hidden');
        }

        function setLoading(buttonId, loading) {
            const button = document.getElementById(buttonId);
            const spinner = document.getElementById(`${buttonId.replace('-button', '')}-loading-spinner`);
            if (!button) return;

            button.disabled = loading;
            if (loading) {
                spinner?.classList.remove('hidden');
                button.classList.add('cursor-not-allowed');
            } else {
                spinner?.classList.add('hidden');
                button.classList.remove('cursor-not-allowed');
            }
        }

        function setStep(step) {
            currentStep = step;
            document.querySelectorAll('.form-container').forEach(el => el.classList.remove('active'));

            if (step === 1) {
                document.getElementById('step-1').classList.add('active');
                document.getElementById('login-heading').textContent = 'Sign in to your Account';
                document.getElementById('login-subheading').textContent = 'Enter your email or phone number';
                resendOtpButton.classList.add('hidden');
                clearInterval(resendTimer);
                checkLoginKey();
            } else if (step === 2) { 
                document.getElementById('step-3-verification').classList.add('active');
                document.getElementById('login-heading').textContent = 'Verify Identity';
                document.getElementById('login-subheading').textContent = 'Enter your password or the OTP sent to your device.';
            }
        }

        function checkLoginKey() {
            const key = loginKeyInput.value.trim();
            nextButton.disabled = key.length < 5;
        }

        window.goToStep1 = function() {
            setStep(1);
            clearMessage();
            credentialInput.value = '';
            loginKeyInput.value = lastEnteredLoginKey;
            loginKeyInput.disabled = false;
            loginKeyInput.focus();
            checkLoginKey();
        }

        function checkCredentialInput() {
            const val = credentialInput.value.trim();
            const isOTP = step2MethodInput.value === 'otp';

            if (isOTP && (val.length === 4 || val.length === 6)) {
                loginButton.disabled = false;
            } else if (!isOTP && val.length >= 4) { 
                loginButton.disabled = false;
            } else {
                loginButton.disabled = true;
            }
        }

        function setVerificationFields(data) {
            const isOTP = data.step_2_method === 'otp';

            // Set hidden fields
            loginKeyTypeInput.value = data.login_key_type;
            step2MethodInput.value = data.step_2_method;
            businessIdInput.value = data.business_id;
            roleInput.value = data.role; 
            loginKeyValueInput.value = data.login_key_value; 

            // Set UI fields
            credentialInput.type = isOTP ? 'text' : 'password';
            credentialInput.placeholder = isOTP ? 'Enter 4 or 6-digit OTP' : 'Enter your password';
            credentialLabel.textContent = isOTP ? 'OTP Code' : 'Password';
            loginKeyDisplay.textContent = `Using: ${data.login_key_value}`; //(${data.role || 'User'})
            credentialInput.value = ''; 
            
            // Handle Resend OTP button visibility and timer
            if (isOTP) {
                resendOtpButton.classList.remove('hidden');
                startResendTimer(60); 
            } else {
                resendOtpButton.classList.add('hidden');
            }

            checkCredentialInput();
            setStep(2); 
            showMessage(data.message, 'success');
        }

        function startResendTimer(duration) {
            let timer = duration;
            resendOtpButton.disabled = true;
            resendTimerSpan.textContent = `(${timer}s)`;
            
            if (resendTimer) clearInterval(resendTimer);
            
            resendTimer = setInterval(() => {
                timer--;
                resendTimerSpan.textContent = `(${timer}s)`;

                if (timer < 0) {
                    clearInterval(resendTimer);
                    resendOtpButton.disabled = false;
                    resendTimerSpan.textContent = '';
                }
            }, 1000);
        }
        
        // --- API Calls ---

        /**
         * @route: /login/identify (Step 1)
         * @FIX: Pass platform_hash_key as a single string value instead of an array.
         */
        async function handleIdentifyUser() {
            clearMessage();
            setLoading('next-button', true);

            const key = loginKeyInput.value.trim();
            lastEnteredLoginKey = key; 

            const url = BASE_URL.endsWith('/') ? BASE_URL + 'login/identify' : BASE_URL + '/login/identify';
            
            let response = null;
            let data = null;

            try {
                response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify({
                        login_key: key,
                        // 👇 THE CRUCIAL FIX: Passing platform key as a single string
                        platform_hash_key: loginPlatformKeyInput.value
                    })
                });

                data = await response.json();

                if (response.ok) {
                    // Assuming direct verification as per simplified UI structure
                    if (data.next_step === 'verification') {
                        setVerificationFields(data);
                    } else if (data.next_step === 'profile_selection') {
                        showMessage('Multiple profiles detected. Please enable Step 2 logic in UI.', 'warning');
                        setVerificationFields(data); 
                    }
                } else {
                    const messageType = (response.status === 404 || response.status === 401) ? 'error' : 'warning';
                    showMessage(data.message || 'Identification failed. Please check your key.', messageType);
                }

            } catch (error) {
                showMessage('Network error or unexpected response from server. Check your backend console.', 'error');
                console.error('Error during identifyUser:', error);
            } finally {
                setLoading('next-button', false);
            }
        }
        
        /**
         * @route: /login/resend-otp (New Feature)
         */
        async function handleResendOtp() {
            clearMessage();
            resendOtpButton.disabled = true;
            resendTimerSpan.textContent = '(Sending...)';

            const url = BASE_URL.endsWith('/') ? BASE_URL + 'login/resend-otp' : BASE_URL + '/login/resend-otp';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        login_key: loginKeyValueInput.value,
                        login_key_type: loginKeyTypeInput.value,
                        user_id: userIdInput.value,
                        business_id: businessIdInput.value,
                        role: roleInput.value
                    })
                });

                const data = await response.json();
                console.log('Resend OTP Response:', data);
                if (response.ok) {
                    showMessage(data.message || 'New OTP sent successfully! (Check server logs)', 'success');
                    startResendTimer(60); 
                } else {
                    showMessage(data.message || 'Failed to resend OTP. Check Resend route.', 'error');
                    resendOtpButton.disabled = false; 
                    resendTimerSpan.textContent = '';
                }
            } catch (error) {
                showMessage('Network error during Resend OTP.', 'error');
                console.log('Error during Resend OTP: ,', error);
                resendOtpButton.disabled = false;
                resendTimerSpan.textContent = '';
            }
        }

        /**
         * @route: /login/finalize (Step 2/3)
         */
        async function handleFinalizeLogin() {
            clearMessage();
            setLoading('login-button', true);

            const payload = {
                login_key: loginKeyValueInput.value,
                credential: credentialInput.value.trim(),
                step_2_method: step2MethodInput.value,
                business_id: businessIdInput.value, 
                role: roleInput.value, 
            };

            const url = BASE_URL.endsWith('/') ? BASE_URL + 'login/finalize' : BASE_URL + '/login/finalize';

            let response = null;
            let data = null;

            try {
                response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                data = await response.json();

                if (response.ok) {
                    showMessage(data.message, 'success');
                    // FIX: Stop loading spinner, and KEEP button disabled before redirecting
                    document.getElementById('login-loading-spinner').classList.add('hidden');
                    loginButton.disabled = true; 
                    clearInterval(resendTimer);

                    // Redirect on success
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    // Display error message
                    showMessage(data.message || 'Login failed. Invalid credential.', 'error');
                    credentialInput.value = ''; // Clear credential on failure
                    checkCredentialInput(); // Re-validate
                }

            } catch (error) {
                showMessage('Network error or unexpected response during final login.', 'error');
                console.error('Error during finalizeLogin:', error);
            } finally {
                // IMPORTANT: Only reset the loading state IF the login was NOT successful.
                if (response === null || !response.ok) {
                    setLoading('login-button', false);
                }
            }
        }

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', () => {
            setStep(1);
            loginKeyInput.value = '';
            checkLoginKey();
        });
    </script>
</body>

</html>