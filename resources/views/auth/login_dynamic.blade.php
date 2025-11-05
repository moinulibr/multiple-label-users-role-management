<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Login - Final Design Fixed</title>
    <meta id="base-url" content="{{ url('/') }}">

    <style>
        /* --- General Variables & Reset --- */
        :root {
            /* Primary Colors - Lavender/Purple */
            --cual-primary-light: #9d78e7; 
            --cual-primary-dark: #6366f1;  
            --cual-link-color: #9d78e7; 
            
            /* Button Colors (Lavender shade) */
            --cual-button-bg: #9d78e7; 
            --cual-button-hover: #7b58c7; 
            --cual-button-focus: #7c3aed; 
            
            /* Gray & Text Colors */
            --cual-body-bg: #f6f7fb; 
            --cual-gray-100: #f3f4f6;
            --cual-gray-300: #d1d5db;
            --cual-gray-500: #6b7280;
            --cual-900: #111827; 
            
            /* Alert Colors for better aesthetics */
            --color-error-bg: #fef2f2;
            --color-error-text: #ef4444;
            --color-error-border: #fca5a5;

            --color-success-bg: #f0fdf4;
            --color-success-text: #16a34a;
            --color-success-border: #86efac;

            --color-warning-bg: #fffbe9;
            --color-warning-text: #f59e0b;
            --color-warning-border: #fcd34d;


            /* Spacing */
            --cual-space-1: 0.25rem;
            --cual-space-2: 0.5rem;
            --cual-space-4: 1rem;
            --cual-space-6: 1.5rem;
            --cual-space-8: 2rem;
            --cual-space-9: 2.25rem;
        }

        /* --- Base & Layout --- */
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            background-color: var(--cual-body-bg); 
            line-height: 1.5; 
        }

        /* Login Card */
        .cual-login-card {
            max-width: 20rem; /* Fixed width (320px) */
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 2rem; 
            padding: 2.5rem 2rem; 
            background-color: #fff; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); 
            border-radius: 1.5rem; 
            border: 1px solid var(--cual-gray-100); 
        }

        /* --- Headings --- */
        .cual-text-center {
            text-align: center;
        }

        .cual-icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 0.5rem; 
        }

        .cual-icon-svg {
            height: 2.5rem; 
            width: 2.5rem; 
            fill: var(--cual-primary-dark);
        }
        
        .cual-heading {
            font-size: 1.25rem; 
            font-weight: 700; 
            color: var(--cual-900); 
            margin-top: 0; 
            margin-bottom: 0.5rem;
        }
        
        .cual-subheading {
            margin-top: 0.5rem; 
            font-size: 0.875rem; 
            color: var(--cual-gray-500); 
            line-height: 1.25;
        }
        
        /* --- Form Containers (Steps) --- */
        .cual-form-space-y-6 {
            display: flex;
            flex-direction: column;
            gap: 1.5rem; /* space-y-6 */
        }

        .cual-form-container {
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
            min-height: 100px; 
            position: relative; 
        }

        .cual-form-container.active {
            display: flex;
            flex-direction: column;
            opacity: 1;
        }

        /* --- Inputs & Focus --- */
        .cual-input-label-row {
            display: flex;
            justify-content: space-between; 
            align-items: center;
            margin-bottom: 0.25rem;
        }
        
        .cual-input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--cual-900);
            text-align: left;
            margin-bottom: 0; 
        }
        
        /* *** আপনার অনুরোধ অনুযায়ী সংশোধিত CSS *** */
        .cual-input-field {
            display: block;
            width: calc(100% - 20px); 
            padding: 0.5rem 0rem; 
            border: 1px solid var(--cual-gray-300); 
            border-radius: 0.375rem; 
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); 
            color: var(--cual-900);
            background-color: var(--cual-gray-100); 
            font-size: 0.875rem; 
            padding-left: 10px; 
            padding-right: 10px; 
        }
        /* *************************************** */
        
        .cual-input-field:focus {
            outline: none;
            border-color: var(--cual-button-focus);
            box-shadow: 0 0 0 1px var(--cual-button-focus);
            background-color: #fff; 
        }

        .cual-input-group-step-2 {
            margin-bottom: 0.75rem; 
        }
        
        .cual-login-key-display {
            display: block;
            font-size: 0.75rem; 
            color: var(--cual-gray-500);
            margin-bottom: var(--cual-space-1);
        }

        /* --- Buttons & Links --- */
        .cual-btn-primary {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0.75rem 1rem; 
            border: 1px solid transparent;
            border-radius: 0.375rem; 
            font-size: 1rem; 
            font-weight: 500; 
            color: #fff;
            background-color: var(--cual-primary-dark); 
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            margin-top: 0.5rem; 
        }

        .cual-btn-sign-in {
            background-color: var(--cual-button-bg); 
        }
        .cual-btn-sign-in:hover {
             background-color: var(--cual-button-hover);
        }
        
        .cual-link-text {
            font-size: 0.875rem; 
            color: var(--cual-link-color); 
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            padding: 0;
            background: none;
            border: none;
            line-height: 1;
        }

        /* ছোট লিংকের জন্য ফন্ট সাইজ (Change/Retype & Resend OTP) */
        .cual-link-text-sm {
            font-size: 0.85rem; /* সামান্য বড় করা হলো */
            line-height: 1;
        }

        .cual-flex-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem; 
            position: relative;
        }
        
        /* Checkbox style */
        .cual-checkbox-label {
            font-size: 0.875rem;
            color: var(--cual-gray-500);
            font-weight: 400;
        }
        .cual-checkbox-container {
            display: flex;
            align-items: center;
        }
        .cual-checkbox-container input[type="checkbox"] {
            margin-right: 0.5rem;
            width: 1rem;
            height: 1rem;
        }
        
        /* OTP Options Container Styling */
        .cual-otp-options-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 0.5rem;
            height: 1.5rem;
        }

        /* --- Message Box Aesthetics (Improved) --- */
        .cual-message-box {
            padding: 0.75rem 1rem; 
            border-radius: 0.5rem; 
            border-width: 1px;
            font-size: 0.875rem; 
            line-height: 1.25rem;
            border-left: 5px solid; 
            margin-bottom: 1rem; 
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Error Style */
        .cual-message-box.error {
            border-color: var(--color-error-border); 
            background-color: var(--color-error-bg); 
            color: var(--color-error-text); 
        }
        .cual-message-box.error svg { fill: var(--color-error-text); }

        /* Success Style */
        .cual-message-box.success {
            border-color: var(--color-success-border); 
            background-color: var(--color-success-bg); 
            color: var(--color-success-text); 
        }
        .cual-message-box.success svg { fill: var(--color-success-text); }

        /* Warning Style */
        .cual-message-box.warning {
            border-color: var(--color-warning-border); 
            background-color: var(--color-warning-bg); 
            color: var(--color-warning-text); 
        }
        .cual-message-box.warning svg { fill: var(--color-warning-text); }
        
        .cual-message-icon {
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Hidden Utilities */
        .cual-hidden {
            display: none !important;
        }
        
        /* --- Responsive Design (max-width: 640px) --- */
        @media (max-width: 640px) {
            body {
                padding: 1rem 0;
            }
            .cual-login-card {
                padding: 2rem 1.5rem; 
                margin: 0 1rem;
                max-width: 95%; 
            }
        }
    </style>
</head>

<body class="cual-body-layout">
    <div class="cual-login-card">
        <div class="cual-text-center">
            <div class="cual-icon-wrapper">
                <!-- Icon (Placeholder) -->
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="cual-icon-svg">
                    <rect x="5" y="5" width="4" height="14" rx="1" fill="#9d78e7"/>
                    <rect x="15" y="5" width="4" height="14" rx="1" fill="#9d78e7"/>
                    <rect x="10" y="5" width="4" height="14" rx="1" fill="#6366f1"/>
                </svg>
            </div>
            <h2 class="cual-heading" id="cual-login-heading">
                Sign in to your Account
            </h2>
            <p class="cual-subheading" id="cual-login-subheading">
                Enter your email or phone number
            </p>
        </div>

        <!-- মেসেজ বক্স -->
        <div id="cual-message-box" class="cual-message-box cual-hidden" role="alert">
            <svg id="cual-message-icon" class="cual-message-icon" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <!-- Icon will be set by JS -->
            </svg>
            <span id="cual-message-text"></span>
        </div>

        <form class="cual-form-space-y-6" id="cual-login-form" onsubmit="event.preventDefault(); handleFinalizeLogin()">
            <!-- Hidden Inputs for State Management -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="cual-login-key-type-input" name="login_key_type">
            <input type="hidden" id="cual-step-2-method-input" name="step_2_method">
            <input type="hidden" id="cual-user-id-input" name="user_id">
            <input type="hidden" id="cual-business-id-input" name="business_id">
            <input type="hidden" id="cual-role-input" name="role">
            <input type="hidden" id="cual-login-key-value-input" name="login_key_value">
            <input type="hidden" id="cual-login-platform-hash-key-input" name="platform_hash_key"
                value="5eaaf16a98fae359e253d21e6bccb2c2">

            <!-- Step 1: Login Key Input -->
            <div id="cual-step-1" class="cual-form-container active">
                
                <div class="cual-input-label-row">
                    <label for="cual-login-key" class="cual-input-label">Email or Phone Number</label>
                    
                    <!-- স্টেপ ১ এর জন্য Change/Retype? বাটন (ডিফল্টভাবে লুকানো থাকবে) -->
                    <button type="button" id="cual-change-key-button-step1" 
                            class="cual-hidden cual-link-text cual-link-text-sm" 
                            onclick="goToStep1()">
                        Change/Retype?
                    </button>
                </div>

                <div class="cual-input-group">
                    <input id="cual-login-key" type="text" autocomplete="username" required
                        class="cual-input-field" placeholder="admin@gmail.com" oninput="checkLoginKey()">
                </div>
                
                <button type="button" id="cual-next-button" onclick="handleIdentifyUser()"
                    class="cual-btn-primary" disabled>
                    Continue
                    <span id="cual-next-loading-spinner" class="cual-hidden cual-ml-2 cual-spinner cual-spinner-white-border"></span>
                </button>
            </div>

            <!-- Step 2/3: Verification (Password or OTP) -->
            <div id="cual-step-3-verification" class="cual-form-container">
                
                <div class="cual-input-label-row">
                    <label id="cual-credential-label" for="cual-credential"
                        class="cual-input-label">
                        Password
                    </label>
                    
                    <!-- স্টেপ ২ এর জন্য Change/Retype? বাটন (স্টেপ ২ এ গেলে দেখাবে) -->
                    <button type="button" id="cual-change-key-button" 
                            class="cual-hidden cual-link-text cual-link-text-sm" 
                            onclick="goToStep1()">
                        Change/Retype?
                    </button>
                </div>
                
                <span id="cual-login-key-display" class="cual-login-key-display">
                    Using: admin@gmail.com
                </span>

                <div class="cual-input-group-step-2">
                    <input id="cual-credential" type="password" autocomplete="current-password" required
                        name="credential" class="cual-input-field" placeholder="Enter your password"
                        oninput="checkCredentialInput()">
                </div>
                
                <!-- Remember Me / Forgot Password / Resend OTP এর জন্য ডাইনামিক কন্টেইনার -->
                <div id="cual-dynamic-options" class="cual-flex-options">
                    
                    <!-- 1. Password Options (Email) -->
                    <div id="cual-password-options" class="cual-flex-options" style="width: 100%;">
                        <div class="cual-checkbox-container">
                            <input id="remember-me" name="remember" type="checkbox">
                            <label for="remember-me" class="cual-checkbox-label">
                                Remember me
                            </label>
                        </div>
    
                        <a href="/forgot-password" id="cual-forgot-password-link" class="cual-link-text">
                            Forgot password?
                        </a>
                    </div>

                    <!-- 2. OTP Options (Phone) -->
                    <div id="cual-otp-options" class="cual-otp-options-container cual-hidden">
                        <button type="button" id="cual-resend-otp-button" class="cual-link-text cual-link-text-sm"
                            onclick="handleResendOtp()" disabled>
                            Resend OTP <span id="cual-resend-timer" class="cual-ml-2"></span>
                        </button>
                    </div>

                </div>
                

                <div>
                    <button type="submit" id="cual-login-button" class="cual-btn-primary cual-btn-sign-in" disabled>
                        Sign In
                        <span id="cual-login-loading-spinner" class="cual-hidden cual-ml-2 cual-spinner cual-spinner-white-border"></span>
                    </button>
                </div>
            </div>
        </form>

        <div class="cual-text-center" style="padding-top: var(--cual-space-1);">
            <p class="cual-subheading">
                Don't have an account yet? 
                <a href="/register" class="cual-link-text">
                    Sign Up
                </a>
            </p>
        </div>
    </div>


    <script>
        // --- Element Declarations (cual- prefix) ---
        const loginPlatformKeyInput = document.getElementById('cual-login-platform-hash-key-input');
        const loginKeyInput = document.getElementById('cual-login-key');
        const credentialInput = document.getElementById('cual-credential');
        const nextButton = document.getElementById('cual-next-button');
        const loginButton = document.getElementById('cual-login-button');
        
        // Dynamic Options
        const passwordOptionsDiv = document.getElementById('cual-password-options');
        const otpOptionsDiv = document.getElementById('cual-otp-options');
        const resendOtpButton = document.getElementById('cual-resend-otp-button');
        const resendTimerSpan = document.getElementById('cual-resend-timer');
        const changeKeyButtonStep1 = document.getElementById('cual-change-key-button-step1');
        const changeKeyButtonStep2 = document.getElementById('cual-change-key-button');


        const messageBox = document.getElementById('cual-message-box');
        const messageText = document.getElementById('cual-message-text');
        const messageIcon = document.getElementById('cual-message-icon');

        const credentialLabel = document.getElementById('cual-credential-label');
        const loginKeyDisplay = document.getElementById('cual-login-key-display');
        const csrfToken = document.querySelector('input[name="_token"]').value;

        const BASE_URL = document.getElementById('base-url').content;

        // Hidden Inputs
        const loginKeyTypeInput = document.getElementById('cual-login-key-type-input');
        const step2MethodInput = document.getElementById('cual-step-2-method-input');
        const userIdInput = document.getElementById('cual-user-id-input');
        const businessIdInput = document.getElementById('cual-business-id-input');
        const roleInput = document.getElementById('cual-role-input');
        const loginKeyValueInput = document.getElementById('cual-login-key-value-input');
        const loginHeading = document.getElementById('cual-login-heading');
        const loginSubheading = document.getElementById('cual-login-subheading');

        // Dynamic States
        let currentStep = 1;
        let lastEnteredLoginKey = '';
        let resendTimer = null;

        // Message Icon SVGs
        const icons = {
            success: '<path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"/>',
            error: '<path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z"/>',
            warning: '<path d="M1 21H23L12 2L1 21ZM13 18H11V16H13V18ZM13 14H11V10H13V14Z"/>'
        };

        // --- Utility Functions ---

        function showMessage(message, type = 'success') {
            messageText.textContent = message;
            messageIcon.innerHTML = icons[type] || icons.success;

            // Reset classes
            messageBox.className = 'cual-message-box'; // Base class
            messageBox.classList.add(type);
            messageBox.classList.remove('cual-hidden');
        }

        function clearMessage() {
            messageBox.classList.add('cual-hidden');
            messageText.textContent = '';
            messageIcon.innerHTML = '';
        }

        function setLoading(buttonId, loading) {
            const button = document.getElementById(buttonId);
            const spinner = document.getElementById(`${buttonId}-loading-spinner`);
            if (!button) return;

            button.disabled = loading;
            if (loading) {
                spinner?.classList.remove('cual-hidden');
            } else {
                spinner?.classList.add('cual-hidden');
            }
        }

        function setStep(step) {
            currentStep = step;
            document.querySelectorAll('.cual-form-container').forEach(el => el.classList.remove('active'));

            if (step === 1) {
                document.getElementById('cual-step-1').classList.add('active');
                loginHeading.textContent = 'Sign in to your Account';
                loginSubheading.textContent = 'Enter your email or phone number';
                
                changeKeyButtonStep2.classList.add('cual-hidden');
                changeKeyButtonStep1.classList.add('cual-hidden');

                clearInterval(resendTimer);
                otpOptionsDiv.classList.add('cual-hidden'); // OTP options hide
                checkLoginKey();
            } else if (step === 2) {
                document.getElementById('cual-step-3-verification').classList.add('active');
                loginHeading.textContent = 'Verify Identity';
                loginSubheading.textContent = 'Enter your password or the OTP sent to your device.';
                
                changeKeyButtonStep1.classList.add('cual-hidden');
                changeKeyButtonStep2.classList.remove('cual-hidden');
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
            loginKeyDisplay.textContent = `Using: ${data.login_key_value}`; 
            credentialInput.value = '';

            // Handle Dynamic Options (Your Logic)
            if (isOTP) {
                // Phone (OTP)
                passwordOptionsDiv.classList.add('cual-hidden'); 
                otpOptionsDiv.classList.remove('cual-hidden'); 
                startResendTimer(10);
            } else {
                // Email (Password)
                passwordOptionsDiv.classList.remove('cual-hidden'); 
                otpOptionsDiv.classList.add('cual-hidden'); 
                clearInterval(resendTimer);
            }

            checkCredentialInput();
            setStep(2);
            showMessage(data.message, 'success');
        }

        function startResendTimer(duration) {
            let timer = duration;
            resendOtpButton.disabled = true;
            resendTimerSpan.textContent = `: (${timer}s)`; // Added : and ()

            if (resendTimer) clearInterval(resendTimer);

            resendTimer = setInterval(() => {
                timer--;
                if (timer >= 0) {
                    resendTimerSpan.textContent = `: (${timer}s)`;
                    resendOtpButton.textContent = `Resend OTP :  (${timer}s)`;
                    timer--;
                }
                
                if (timer < 0) {
                    clearInterval(resendTimer);
                    resendOtpButton.disabled = false;
                    resendOtpButton.textContent = 'Resend OTP'; // Changed text when timer ends
                    //resendTimerSpan.textContent = '';
                }
            }, 1000);
        }
        
        // --- API Calls (unchanged logic) ---

        async function handleIdentifyUser() {
            clearMessage();
            setLoading('cual-next-button', true);

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
                        platform_hash_key: loginPlatformKeyInput.value
                    })
                });

                data = await response.json();

                if (response.ok) {
                    if (data.next_step === 'verification' || data.next_step === 'profile_selection') {
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
                setLoading('cual-next-button', false);
            }
        }
        
        async function handleResendOtp() {
            clearMessage();
            resendOtpButton.disabled = true;
            resendOtpButton.textContent = 'Resending...';
            resendTimerSpan.textContent = '';

            startResendTimer(10);
            
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
                
                if (response.ok) {
                    showMessage(data.message || 'New OTP sent successfully! (Check server logs)', 'success');
                    resendOtpButton.textContent = 'Resend OTP';
                } else {
                    showMessage(data.message || 'Failed to resend OTP. Check Resend route.', 'error');
                    // Stop the timer if fail
                    clearInterval(resendTimer);
                    resendTimerSpan.textContent = '';
                    resendOtpButton.disabled = false;
                    resendOtpButton.textContent = 'Resend OTP';
                }
            } catch (error) {
                showMessage('Network error during Resend OTP.', 'error');
                clearInterval(resendTimer);
                resendTimerSpan.textContent = '';
                resendOtpButton.disabled = false;
                resendOtpButton.textContent = 'Resend OTP';
            }
        }

        async function handleFinalizeLogin() {
            clearMessage();
            setLoading('cual-login-button', true);

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
                    
                    loginButton.disabled = true; 
                    setLoading('cual-login-button', true);
                    clearInterval(resendTimer);

                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    showMessage(data.message || 'Login failed. Invalid credential.', 'error');
                    credentialInput.value = ''; 
                    checkCredentialInput(); 
                    setLoading('cual-login-button', false); 
                }

            } catch (error) {
                showMessage('Network error or unexpected response during final login.', 'error');
                console.error('Error during finalizeLogin:', error);
                setLoading('cual-login-button', false);
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
