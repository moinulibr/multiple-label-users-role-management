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
            transform: translateY(10px);
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        .form-container.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .select-option {
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s;
            border: 2px solid transparent;
        }

        .select-option:hover {
            background-color: #f3f4f6;
        }

        .select-option.selected {
            border-color: #4f46e5;
            /* indigo-600 */
            background-color: #eef2ff;
            /* indigo-50 */
        }

        .role-tag {
            padding: 4px 8px;
            border-radius: 9999px;
            background-color: #e0e7ff;
            /* indigo-100 */
            color: #4f46e5;
            /* indigo-600 */
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Custom styling for different select options */
        .general-profile {
            background-color: #fffbeb;
            /* amber-50 */
            border-color: #f59e0b;
            /* amber-500 */
        }

        .general-profile.selected {
            background-color: #fef3c7;
            /* amber-100 */
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-6 bg-white shadow-xl rounded-xl border border-gray-200">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900" id="login-heading">
                Dynamic Login
            </h2>
            <p class="mt-2 text-sm text-gray-600" id="login-subheading">
                ইমেল বা ফোন নম্বর দিয়ে শুরু করুন
            </p>
        </div>

        <!-- Error and Message Display -->
        <div id="message-box" class="hidden p-3 rounded-md border text-sm" role="alert"></div>

        <form class="space-y-6" id="login-form" onsubmit="event.preventDefault(); handleFinalizeLogin()">
            <!-- Hidden Inputs for State Management -->
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
                    ইমেল বা ফোন নম্বর
                </label>
                <div class="mt-1">
                    <input id="login-key" type="text" autocomplete="username" required
                        class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="আপনার ইমেল বা ফোন নম্বর লিখুন"
                        oninput="checkLoginKey()">
                </div>
                <!-- Button: Next Step -->
                <button type="button" id="next-button" onclick="handleIdentifyUser()"
                    class="w-full mt-6 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                    disabled>
                    পরবর্তী
                    <span id="next-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
            </div>

            <!-- 2. Step 2: Profile Selection (Complex/General/Same-Business) -->
            <div id="step-2-profile-selection" class="form-container space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">আপনার প্রোফাইল নির্বাচন করুন</h3>

                <div id="selection-list" class="space-y-4 max-h-80 overflow-y-auto">
                    <!-- Dynamic profile/business/role list goes here -->
                </div>

                <button type="button" id="select-profile-button" onclick="handleProfileSelection()"
                    class="w-full mt-4 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    disabled>
                    যাচাইকরণে যান
                    <span id="profile-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
                <div class="flex justify-end pt-2">
                    <!-- "Change Email/Phone" button fixed to bottom right -->
                    <button type="button" id="change-key-button-2" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium" onclick="goToStep1()">
                        ইমেল/ফোন পরিবর্তন করুন
                    </button>
                </div>
            </div>

            <!-- 3. Step 3: Verification (Password/OTP) -->
            <div id="step-3-verification" class="form-container space-y-6">
                <!-- Dynamic Label and Input -->
                <div>
                    <div class="flex justify-between items-center">
                        <label id="credential-label" for="credential" class="block text-sm font-medium text-gray-700">
                            পাসওয়ার্ড
                        </label>
                    </div>
                    <div class="mt-1">
                        <input id="credential" type="password" autocomplete="current-password" required
                            name="credential"
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="আপনার পাসওয়ার্ড বা OTP লিখুন"
                            oninput="checkCredentialInput()">
                    </div>
                </div>

                <!-- Display Selected Profile and Change Button (Fixed to bottom right) -->
                <div class="flex justify-end items-center text-right">
                    <div class="space-y-1">
                        <p id="login-key-display" class="text-xs text-gray-600 font-medium"></p>
                        <!-- "Change Email/Phone" button fixed to bottom right of this box -->
                        <button type="button" id="change-key-button"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                            onclick="goToStep1()">
                            ইমেল/ফোন পরিবর্তন করুন
                        </button>
                    </div>
                </div>

                <!-- Button: Login -->
                <div>
                    <button type="submit" id="login-button"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        disabled>
                        লগইন করুন
                        <span id="login-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const loginKeyInput = document.getElementById('login-key');
        const credentialInput = document.getElementById('credential');
        const nextButton = document.getElementById('next-button');
        const loginButton = document.getElementById('login-button');
        const selectProfileButton = document.getElementById('select-profile-button');

        const messageBox = document.getElementById('message-box');
        const credentialLabel = document.getElementById('credential-label');
        const loginKeyDisplay = document.getElementById('login-key-display');
        const selectionList = document.getElementById('selection-list');

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
        let allGroupedProfiles = []; // Stores all profiles from step 1
        let lastEnteredLoginKey = '';
        let currentSelectionType = ''; // 'general', 'same_business', or 'different_business'

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
            const spinner = document.getElementById(buttonId.replace('-button', '-loading-spinner'));
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
            // Hide all step containers
            document.getElementById('step-1').classList.remove('active');
            document.getElementById('step-2-profile-selection').classList.remove('active');
            document.getElementById('step-3-verification').classList.remove('active');

            // Manage "Change Email/Phone" button visibility
            document.getElementById('change-key-button').classList.add('hidden'); // Step 3
            document.getElementById('change-key-button-2').classList.add('hidden'); // Step 2

            if (step === 1) {
                document.getElementById('step-1').classList.add('active');
                document.getElementById('login-heading').textContent = 'Dynamic Login';
                document.getElementById('login-subheading').textContent = 'ইমেল বা ফোন নম্বর দিয়ে শুরু করুন';
                checkLoginKey();
            } else if (step === 2) {
                document.getElementById('step-2-profile-selection').classList.add('active');
                document.getElementById('change-key-button-2').classList.remove('hidden');
                document.getElementById('login-heading').textContent = 'প্রোফাইল নির্বাচন';
                document.getElementById('login-subheading').textContent = 'আপনার পছন্দের প্রোফাইল নির্বাচন করুন';
            } else if (step === 3) {
                document.getElementById('step-3-verification').classList.add('active');
                document.getElementById('change-key-button').classList.remove('hidden');
                document.getElementById('login-heading').textContent = 'পরিচয় যাচাই';
                document.getElementById('login-subheading').textContent = 'আপনার পাসওয়ার্ড বা OTP দিন';
            }
        }

        // --- Step 1: Input Validation ---

        function checkLoginKey() {
            const key = loginKeyInput.value.trim();
            nextButton.disabled = key.length < 5;
        }

        // --- Go Back Functionality ---
        window.goToStep1 = function() {
            setStep(1);
            clearMessage();
            credentialInput.value = '';
            // FIX: Retain the last successful key in the input field
            loginKeyInput.value = lastEnteredLoginKey;
            loginKeyInput.disabled = false;
            loginKeyInput.focus();
            checkLoginKey();
            // Reset selection state
            businessIdInput.value = '';
            roleInput.value = '';
        }

        // --- Step 2: Profile Selection & Rendering ---

        function updateSelectedProfile(businessId, role) {
            // Unselect all previous selections
            document.querySelectorAll('.select-option').forEach(el => el.classList.remove('selected'));

            // Find the newly selected element
            let selectedElement = null;
            if (businessId === null) {
                // For general profiles, we use the role as the key since businessId is null
                selectedElement = document.querySelector(`.general-profile[data-role="${role}"]`);
            } else if (currentSelectionType === 'same_business') {
                // Case 3: Only roles are selectable
                selectedElement = document.querySelector(`.role-option[data-role="${role}"]`);
            } else {
                // Case 4: Business/Role pair is selectable
                selectedElement = document.querySelector(`.business-role-pair[data-business-id="${businessId}"][data-role="${role}"]`);
            }

            if (selectedElement) {
                selectedElement.classList.add('selected');
                selectProfileButton.disabled = false;

                // Set hidden inputs
                businessIdInput.value = businessId === null ? 'null' : businessId;
                roleInput.value = role;
            } else {
                selectProfileButton.disabled = true;
                businessIdInput.value = '';
                roleInput.value = '';
            }
        }

        // Renders the appropriate view for Step 2 based on profile complexity
        function renderProfileSelection(data) {
            allGroupedProfiles = data.profiles;
            selectionList.innerHTML = '';
            selectProfileButton.disabled = true;
            businessIdInput.value = '';
            roleInput.value = '';

            if (data.is_all_general) {
                currentSelectionType = 'general';
                renderGeneralSelection(data.profiles); // Case 2
            } else if (data.is_same_business_multiple_roles) {
                currentSelectionType = 'same_business';
                renderSameBusinessRoleSelection(data.profiles[0]); // Case 3
            } else {
                currentSelectionType = 'different_business';
                renderDifferentBusinessSelection(data.profiles); // Case 4
            }

            setStep(2);
        }

        // Case 2: Multiple General Profiles (business_id = null)
        function renderGeneralSelection(profiles) {
            profiles.forEach(profileGroup => {
                const role = profileGroup.roles[0];
                const businessId = profileGroup.business_id; // Will be null

                const element = document.createElement('div');
                element.className = 'select-option general-profile p-4 rounded-xl flex justify-between items-center shadow-md text-gray-800';
                element.setAttribute('data-business-id', businessId);
                element.setAttribute('data-role', role);

                element.innerHTML = `
                    <div>
                        <div class="font-bold text-base">${profileGroup.business_name}</div>
                        <div class="mt-1 text-sm text-yellow-700">${role}</div>
                    </div>
                `;

                element.onclick = () => {
                    updateSelectedProfile(businessId, role);
                };
                selectionList.appendChild(element);
            });
        }

        // Case 3: Same Business, Multiple Roles
        function renderSameBusinessRoleSelection(businessGroup) {
            // Display Business Name only once at the top
            selectionList.innerHTML = `<div class="bg-indigo-50 p-3 rounded-lg font-bold text-indigo-800 border-l-4 border-indigo-500 mb-4">${businessGroup.business_name}</div>`;

            businessGroup.roles.forEach(role => {
                const element = document.createElement('div');
                element.className = 'select-option role-option p-3 rounded-xl flex items-center shadow-sm text-gray-700';
                element.setAttribute('data-business-id', businessGroup.business_id);
                element.setAttribute('data-role', role);

                element.innerHTML = `
                    <div class="font-semibold text-base">${role}</div>
                `;

                element.onclick = () => {
                    updateSelectedProfile(businessGroup.business_id, role);
                };
                selectionList.appendChild(element);
            });
        }

        // Case 4: Different Business IDs (Business & Role Pair Selection)
        function renderDifferentBusinessSelection(profiles) {
            profiles.forEach(profileGroup => {
                // Outer container for the business
                const businessContainer = document.createElement('div');
                businessContainer.className = 'p-4 border border-gray-200 rounded-xl space-y-2 bg-gray-50';

                businessContainer.innerHTML = `<h4 class="font-bold text-indigo-700">${profileGroup.business_name} (ID: ${profileGroup.business_id})</h4>`;

                // Roles inside the business
                profileGroup.roles.forEach(role => {
                    const element = document.createElement('div');
                    element.className = 'select-option business-role-pair p-3 rounded-lg flex items-center text-sm bg-white shadow-sm';
                    element.setAttribute('data-business-id', profileGroup.business_id);
                    element.setAttribute('data-role', role);

                    element.innerHTML = `
                        <div class="flex-grow">${role}</div>
                    `;

                    element.onclick = () => {
                        updateSelectedProfile(profileGroup.business_id, role);
                    };
                    businessContainer.appendChild(element);
                });

                selectionList.appendChild(businessContainer);
            });
        }

        // --- Step 3: Credential Validation ---

        function checkCredentialInput() {
            const val = credentialInput.value.trim();
            const isOTP = step2MethodInput.value === 'otp';

            // OTP is usually 4 or 6 digits. Password length check is variable, here set to min 4.
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
            const businessId = data.business_id === 'null' ? 'সাধারণ অ্যাকাউন্ট' : data.business_id;

            // Set hidden fields
            loginKeyTypeInput.value = data.login_key_type;
            step2MethodInput.value = data.step_2_method;
            businessIdInput.value = data.business_id;
            roleInput.value = data.role;

            // Set UI fields
            credentialInput.value = ''; // Clear credential input
            credentialInput.type = isOTP ? 'text' : 'password';
            credentialInput.placeholder = isOTP ? '৪ বা ৬-সংখ্যার OTP লিখুন' : 'আপনার পাসওয়ার্ড লিখুন';
            credentialLabel.textContent = isOTP ? 'OTP' : 'পাসওয়ার্ড';

            const keyText = data.login_key_type === 'phone' ? 'ফোন' : 'ইমেল';

            // Display: ডান দিকে নিচে (Right-Bottom)
            loginKeyDisplay.innerHTML = `
                <div class="text-right">
                    <span class="text-gray-900">${keyText}: **${data.login_key_value}**</span>
                    <div class="text-xs text-gray-500">(${data.role} @ ${businessId})</div>
                </div>
            `;

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
                    loginKeyTypeInput.value = data.login_key_type;

                    if (data.next_step.startsWith('profile_selection')) {
                        // Go to Step 2: Profile Selection (Complex/General/Same-Business)
                        renderProfileSelection(data);
                        showMessage(data.message, 'warning');
                    } else if (data.next_step === 'verification') {
                        // Case 1: Single profile, Go directly to Step 3: Verification
                        setVerificationFields(data);
                    }
                } else {
                    const messageType = response.status === 404 ? 'warning' : 'error';
                    showMessage(data.message || 'যাচাইকরণ ব্যর্থ হয়েছে।', messageType);
                }

            } catch (error) {
                showMessage('নেটওয়ার্ক ত্রুটি বা সার্ভার থেকে অপ্রত্যাশিত প্রতিক্রিয়া।', 'error');
                console.error('Error during identifyUser:', error);
            } finally {
                setLoading('next-button', false);
            }
        }

        // Step 2: Select Profile (Business/Role Pair)
        async function handleProfileSelection() {
            clearMessage();
            setLoading('select-profile-button', true);

            if (!businessIdInput.value || !roleInput.value) {
                showMessage('চালিয়ে যেতে দয়া করে একটি প্রোফাইল এবং ভূমিকা নির্বাচন করুন।', 'warning');
                setLoading('select-profile-button', false);
                return;
            }

            const payload = {
                user_id: userIdInput.value,
                login_key_value: loginKeyValueInput.value,
                login_key_type: loginKeyTypeInput.value,
                business_id: businessIdInput.value, // Can be string 'null'
                role: roleInput.value,
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
                    showMessage(data.message || 'প্রোফাইল নির্বাচন ব্যর্থ হয়েছে।', 'error');
                }

            } catch (error) {
                showMessage('নেটওয়ার্ক ত্রুটি বা সার্ভার থেকে অপ্রত্যাশিত প্রতিক্রিয়া।', 'error');
                console.error('Error during selectProfile:', error);
            } finally {
                setLoading('select-profile-button', false);
            }
        }


        // Step 3: Finalize Login
        async function handleFinalizeLogin() {
            clearMessage();
            setLoading('login-button', true);
            loginButton.disabled = true; // Disable immediately to prevent double submission

            const payload = {
                login_key: loginKeyValueInput.value,
                credential: credentialInput.value.trim(),
                step_2_method: step2MethodInput.value,
                business_id: businessIdInput.value, // Can be string 'null'
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
                    // Success: Button remains disabled and redirects
                    // NO need to enable button on success
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                } else {
                    // Failure: Re-enable button
                    showMessage(data.message || 'লগইন ব্যর্থ হয়েছে।', 'error');
                    setLoading('login-button', false);
                    checkCredentialInput(); // Check input again to potentially re-enable if input is valid
                }

            } catch (error) {
                // Failure: Re-enable button
                showMessage('নেটওয়ার্ক ত্রুটি বা সার্ভার থেকে অপ্রত্যাশিত প্রতিক্রিয়া।', 'error');
                console.error('Error during finalizeLogin:', error);
                setLoading('login-button', false);
                checkCredentialInput();
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