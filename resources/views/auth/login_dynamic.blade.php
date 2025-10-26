
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Multi-Profile Login</title>
    <!-- Tailwind CSS CDN (Development Only) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dynamic Base URL Meta Tag -->
    <meta id="base-url" content="{{ url('/') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }

        .form-container {
            /* Keep all form containers hidden initially, JS will manage visibility */
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        .form-container.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .form-container.active.hidden {
            display: none;
        }

        .profile-option {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .profile-option:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-6 bg-white shadow-lg rounded-xl border border-gray-200">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900" id="login-heading">
                Dynamic Login
            </h2>
            <p class="mt-2 text-sm text-gray-600" id="login-subheading">
                Start with your email or phone number
            </p>
        </div>

        <!-- Error and Message Display -->
        <div id="message-box" class="hidden p-3 rounded-md border text-sm" role="alert"></div>

        <form class="space-y-6" id="login-form" onsubmit="event.preventDefault(); handleFinalizeLogin()">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="login-key-type-input" name="login_key_type">
            <input type="hidden" id="step-2-method-input" name="step_2_method">
            <input type="hidden" id="user-id-input" name="user_id">
            <input type="hidden" id="business-id-input" name="business_id">
            <input type="hidden" id="role-input" name="role">
            <input type="hidden" id="login-key-value-input" name="login_key_value">

            <!-- 1. Step 1: Key Input (Email/Phone) -->
            <div id="step-1" class="form-container active">
                <label for="login-key" class="block text-sm font-medium text-gray-700">
                    Email or Phone Number
                </label>
                <div class="mt-1">
                    <input id="login-key" type="text" autocomplete="username" required
                        class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter your email or phone number"
                        oninput="checkLoginKey()">
                </div>
                <!-- Button: Next Step -->
                <button type="button" id="next-button" onclick="handleIdentifyUser()"
                    class="w-full mt-6 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                    disabled>
                    Next
                    <span id="next-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
            </div>

            <!-- 2. Step 2: Profile Selection -->
            <div id="step-2-profile-selection" class="form-container space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Select Your Business/Role</h3>
                <div id="profiles-list" class="space-y-2 border border-gray-200 rounded-md p-3 max-h-60 overflow-y-auto">
                    <!-- Dynamic profile list goes here -->
                </div>
                <button type="button" id="select-profile-button" onclick="handleProfileSelection()"
                    class="w-full mt-4 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    disabled>
                    Continue to Verification
                    <span id="profile-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
            </div>


            <!-- 3. Step 3: Verification (Password/OTP) -->
            <div id="step-3-verification" class="form-container space-y-6">
                <!-- Dynamic Label and Input -->
                <div>
                    <div class="flex justify-between items-center">
                        <div>
                            <label id="credential-label" for="credential" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                        </div>
                        <div>
                            <button type="button" id="change-key-button"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium whitespace-nowrap"
                                onclick="goToStep1()">
                                Change   <span id="login-key-display" class="text-xs text-indigo-600 font-semibold"></span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-1">
                        <input id="credential" type="password" autocomplete="current-password" required
                            name="credential"
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter your password or OTP"
                            oninput="checkCredentialInput()">
                    </div>
                </div>

                <!-- Button: Login -->
                <div>
                    <button type="submit" id="login-button"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        disabled>
                        Login
                        <span id="login-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('login-form');
        const loginKeyInput = document.getElementById('login-key');
        const credentialInput = document.getElementById('credential');
        const changeKeyButton = document.getElementById('change-key-button');
        const nextButton = document.getElementById('next-button');
        const loginButton = document.getElementById('login-button');
        const selectProfileButton = document.getElementById('select-profile-button');

        const messageBox = document.getElementById('message-box');
        const credentialLabel = document.getElementById('credential-label');
        const loginKeyDisplay = document.getElementById('login-key-display');
        const profilesList = document.getElementById('profiles-list');

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
        let selectedProfile = null;
        let lastEnteredLoginKey = '';

        // --- Utility Functions ---

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
        }

        function clearMessage() {
            messageBox.classList.add('hidden');
        }

        function setLoading(buttonId, loading) {
            const button = document.getElementById(buttonId);
            const spinner = document.getElementById(`${buttonId}-spinner`);
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
                changeKeyButton.classList.add('hidden');
                document.getElementById('login-heading').textContent = 'Dynamic Login';
                document.getElementById('login-subheading').textContent = 'Start with your email or phone number';
                checkLoginKey();
            } else if (step === 2) {
                document.getElementById('step-2-profile-selection').classList.add('active');
                changeKeyButton.classList.remove('hidden');
                document.getElementById('login-heading').textContent = 'Select Profile';
                document.getElementById('login-subheading').textContent = 'Please choose your Business/Role';
            } else if (step === 3) {
                document.getElementById('step-3-verification').classList.add('active');
                changeKeyButton.classList.remove('hidden');
                document.getElementById('login-heading').textContent = 'Verify Identity';
            }
        }

        // --- Step 1: Input Validation ---
        function checkLoginKey() {
            const key = loginKeyInput.value.trim();
            nextButton.disabled = key.length < 5;
        }

        // --- Step 1 & 2 & 3: Go Back Functionality (FIX) ---
        window.goToStep1 = function() {
            setStep(1);
            clearMessage();
            credentialInput.value = '';
            // FIX: Retain the last successful key in the input field
            loginKeyInput.value = lastEnteredLoginKey;
            loginKeyInput.disabled = false;
            loginKeyInput.focus();
            checkLoginKey();
        }

        // --- Step 2: Profile Selection & Rendering ---
        function renderProfiles(profiles) {
            profilesList.innerHTML = '';
            selectedProfile = null;
            selectProfileButton.disabled = true;

            profiles.forEach((profile, index) => {
                const element = document.createElement('div');
                element.className = 'profile-option p-3 border rounded-md flex justify-between items-center text-gray-700 hover:border-indigo-400 transition duration-200';
                element.innerHTML = `
                    <div>
                        <div class="font-semibold">${profile.business_name}</div>
                        <div class="text-sm text-gray-500">${profile.role}</div>
                    </div>
                    <input type="radio" name="profile_selection" value="${index}" class="form-radio text-indigo-600 h-4 w-4">
                `;
                element.onclick = () => {
                    document.querySelectorAll('input[name="profile_selection"]').forEach(input => input.checked = false);
                    element.querySelector('input[name="profile_selection"]').checked = true;
                    selectProfileButton.disabled = false;
                    selectedProfile = profile;
                };
                profilesList.appendChild(element);
            });
            setStep(2);
        }

        // --- Step 3: Credential Validation ---

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

            // Set UI fields
            credentialInput.type = isOTP ? 'text' : 'password';
            credentialInput.placeholder = isOTP ? 'Enter 4 or 6-digit OTP' : 'Enter your password';
            credentialLabel.textContent = isOTP ? 'OTP' : 'Password';
            loginKeyDisplay.textContent = `${data.login_key_type === 'phone' ? 'Phone' : 'Email'}: ${data.login_key_value} (${data.role} @ ${data.business_id})`;

            checkCredentialInput();
            setStep(3);
            showMessage(data.message, 'success');
        }

        // --- API Calls ---

        // Step 1: Identify User
        async function handleIdentifyUser() {
            clearMessage();
            setLoading('next-button', true);

            const key = loginKeyInput.value.trim();
            lastEnteredLoginKey = key; // Save current input key

            const url = BASE_URL.endsWith('/') ? BASE_URL + 'login/identify' : BASE_URL + '/login/identify';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        login_key: key
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    loginKeyValueInput.value = data.login_key_value || key; // Save normalized key
                    userIdInput.value = data.user_id;

                    if (data.next_step === 'profile_selection') {
                        // Go to Step 2: Profile Selection
                        renderProfiles(data.profiles);
                        showMessage(data.message, 'warning');
                    } else if (data.next_step === 'verification') {
                        // Go directly to Step 3: Verification
                        setVerificationFields(data);
                    }
                } else {
                    const messageType = response.status === 404 ? 'warning' : 'error';
                    showMessage(data.message || 'Verification failed.', messageType);
                }

            } catch (error) {
                showMessage('Network error or unexpected response from server.', 'error');
                console.error('Error during identifyUser:', error);
            } finally {
                setLoading('next-button', false);
            }
        }

        // Step 2: Select Profile
        async function handleProfileSelection() {
            clearMessage();
            setLoading('select-profile-button', true);

            if (!selectedProfile) {
                showMessage('Please select a profile to continue.', 'warning');
                setLoading('select-profile-button', false);
                return;
            }

            const payload = {
                user_id: userIdInput.value,
                login_key_value: loginKeyValueInput.value,
                login_key_type: loginKeyTypeInput.value,
                business_id: selectedProfile.business_id,
                role: selectedProfile.role,
            };

            const url = BASE_URL.endsWith('/') ? BASE_URL + 'login/select-profile' : BASE_URL + '/login/select-profile';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.next_step === 'verification') {
                    // Go to Step 3: Verification
                    setVerificationFields(data);
                } else {
                    showMessage(data.message || 'Profile selection failed.', 'error');
                }

            } catch (error) {
                showMessage('Network error or unexpected response from server.', 'error');
                console.error('Error during selectProfile:', error);
            } finally {
                setLoading('select-profile-button', false);
            }
        }


        // Step 3: Finalize Login
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

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    showMessage(data.message, 'success');
                    // Redirect on success
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                } else {
                    // Display error message
                    showMessage(data.message || 'Login failed.', 'error');
                }

            } catch (error) {
                showMessage('Network error or unexpected response from server.', 'error');
                console.error('Error during finalizeLogin:', error);
            } finally {
                setLoading('login-button', false);
            }
        }

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Ensure initial step is 1 and validation is checked
            setStep(1);
            loginKeyInput.value = ''; // Clear input on load
            checkLoginKey();
        });
    </script>
</body>

</html>