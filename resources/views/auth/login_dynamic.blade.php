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
        .form-container.active.hidden {
            display: none;
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
            border-color: #4f46e5; /* indigo-600 */
            background-color: #eef2ff; /* indigo-50 */
        }
        .role-tag {
            padding: 4px 8px;
            border-radius: 9999px;
            background-color: #e0e7ff; /* indigo-100 */
            color: #4f46e5; /* indigo-600 */
            font-size: 0.75rem;
            font-weight: 600;
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

            <!-- 2. Step 2A: Business Selection -->
            <div id="step-2-business-selection" class="form-container space-y-4">
                 <h3 class="text-lg font-semibold text-gray-800">আপনার ব্যবসা নির্বাচন করুন</h3>
                 <div id="businesses-list" class="space-y-2 max-h-60 overflow-y-auto">
                    <!-- Dynamic business list goes here -->
                 </div>
                 <button type="button" id="select-business-button" onclick="handleBusinessSelection()"
                        class="w-full mt-4 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        disabled>
                    চালিয়ে যান
                    <span id="business-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
                <div class="flex justify-end pt-2">
                    <!-- "Change Email/Phone" button fixed to bottom right -->
                    <button type="button" id="change-key-button-2" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium" onclick="goToStep1()">
                        ইমেল/ফোন পরিবর্তন করুন
                    </button>
                </div>
            </div>
            
            <!-- 2. Step 2B: Role Selection for a Specific Business -->
            <div id="step-2-role-selection" class="form-container space-y-4">
                 <h3 class="text-lg font-semibold text-gray-800" id="role-selection-heading"></h3>
                 <div id="roles-list" class="space-y-2 max-h-60 overflow-y-auto">
                    <!-- Dynamic role list goes here -->
                 </div>
                 <button type="button" id="select-role-button" onclick="handleRoleSelection()"
                        class="w-full mt-4 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        disabled>
                    যাচাইকরণে যান
                    <span id="role-loading-spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
                <div class="flex justify-between pt-2">
                    <button type="button" class="text-xs text-gray-500 hover:text-gray-700 font-medium" onclick="goToBusinessSelection()">
                        &larr; ব্যবসা পরিবর্তন করুন
                    </button>
                    <!-- "Change Email/Phone" button fixed to bottom right -->
                    <button type="button" id="change-key-button-3" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium" onclick="goToStep1()">
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

                <!-- Display Selected Profile and Change Button -->
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
        const form = document.getElementById('login-form');
        const loginKeyInput = document.getElementById('login-key');
        const credentialInput = document.getElementById('credential');
        const nextButton = document.getElementById('next-button');
        const loginButton = document.getElementById('login-button');
        const selectBusinessButton = document.getElementById('select-business-button');
        const selectRoleButton = document.getElementById('select-role-button');
        
        const messageBox = document.getElementById('message-box');
        const credentialLabel = document.getElementById('credential-label');
        const loginKeyDisplay = document.getElementById('login-key-display');
        const businessesList = document.getElementById('businesses-list');
        const rolesList = document.getElementById('roles-list');

        const BASE_URL = document.getElementById('base-url').content;

        // Hidden Inputs
        const loginKeyTypeInput = document.getElementById('login-key-type-input');
        const step2MethodInput = document.getElementById('step-2-method-input');
        const userIdInput = document.getElementById('user-id-input');
        const businessIdInput = document.getElementById('business-id-input');
        const roleInput = document.getElementById('role-input');
        const loginKeyValueInput = document.getElementById('login-key-value-input');
        
        // Dynamic States
        let currentStep = 1; // 1: Key Input, 2: Profile Selection, 3: Verification
        let currentSubStep = 'business'; // 'business' or 'role'
        let selectedBusiness = null;
        let selectedRole = null;
        let allGroupedProfiles = []; // Stores all profiles from step 1
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
            document.getElementById('step-2-business-selection').classList.remove('active');
            document.getElementById('step-2-role-selection').classList.remove('active');
            document.getElementById('step-3-verification').classList.remove('active');
            
            // Manage "Change Email/Phone" button visibility
            document.getElementById('change-key-button').classList.add('hidden'); // Step 3
            document.getElementById('change-key-button-2').classList.add('hidden'); // Step 2A
            document.getElementById('change-key-button-3').classList.add('hidden'); // Step 2B

            if (step === 1) {
                document.getElementById('step-1').classList.add('active');
                document.getElementById('login-heading').textContent = 'Dynamic Login';
                document.getElementById('login-subheading').textContent = 'ইমেল বা ফোন নম্বর দিয়ে শুরু করুন';
                checkLoginKey();
            } else if (step === 2) {
                if (currentSubStep === 'business') {
                    document.getElementById('step-2-business-selection').classList.add('active');
                    document.getElementById('change-key-button-2').classList.remove('hidden');
                    document.getElementById('login-heading').textContent = 'প্রোফাইল নির্বাচন';
                    document.getElementById('login-subheading').textContent = 'আপনার ব্যবসা নির্বাচন করুন';
                } else if (currentSubStep === 'role') {
                    document.getElementById('step-2-role-selection').classList.add('active');
                    document.getElementById('change-key-button-3').classList.remove('hidden');
                    document.getElementById('login-heading').textContent = 'ভূমিকা নির্বাচন';
                    document.getElementById('login-subheading').textContent = 'আপনার ভূমিকা (Role) নির্বাচন করুন';
                }
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

        // --- Go Back Functionality (FIXED) ---
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

        window.goToBusinessSelection = function() {
            currentSubStep = 'business';
            setStep(2);
            renderBusinesses(allGroupedProfiles); // Re-render the initial business list
            clearMessage();
        }

        // --- Step 2: Business/Role Selection & Rendering ---

        function renderBusinesses(profiles) {
            allGroupedProfiles = profiles;
            businessesList.innerHTML = '';
            selectedBusiness = null;
            selectedRole = null;
            selectBusinessButton.disabled = true;
            
            profiles.forEach((profile) => {
                const element = document.createElement('div');
                element.id = `biz-${profile.business_id}`;
                element.className = 'select-option p-4 rounded-xl flex justify-between items-center bg-white shadow-sm text-gray-800';
                
                const roleTags = profile.roles.map(role => `<span class="role-tag">${role}</span>`).join('');
                
                element.innerHTML = `
                    <div>
                        <div class="font-bold text-base">${profile.business_name}</div>
                        <div class="mt-1 flex flex-wrap gap-2">${roleTags}</div>
                    </div>
                `;
                
                element.onclick = () => {
                    document.querySelectorAll('.select-option').forEach(el => el.classList.remove('selected'));
                    element.classList.add('selected');
                    selectBusinessButton.disabled = false;
                    selectedBusiness = profile;
                };
                businessesList.appendChild(element);
            });
            
            currentSubStep = 'business';
            setStep(2);
        }

        function renderRoles(business) {
            rolesList.innerHTML = '';
            selectedRole = null;
            selectRoleButton.disabled = true;
            
            document.getElementById('role-selection-heading').textContent = `আপনার ভূমিকা নির্বাচন করুন: ${business.business_name}`;

            business.roles.forEach((role) => {
                const element = document.createElement('div');
                element.id = `role-${role}`;
                element.className = 'select-option p-4 rounded-xl flex justify-between items-center bg-white shadow-sm text-gray-800';
                
                element.innerHTML = `
                    <div class="font-bold text-lg">${role}</div>
                `;
                
                element.onclick = () => {
                    document.querySelectorAll('#step-2-role-selection .select-option').forEach(el => el.classList.remove('selected'));
                    element.classList.add('selected');
                    selectRoleButton.disabled = false;
                    selectedRole = role;
                };
                rolesList.appendChild(element);
            });
            
            currentSubStep = 'role';
            setStep(2);
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
            // Display: Email/Phone: key (Role @ Business ID)
            loginKeyDisplay.innerHTML = `${keyText}: **${data.login_key_value}** <br> (${data.role} @ ${data.business_id})`;
            
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
                    body: JSON.stringify({ login_key: key })
                });

                const data = await response.json();

                if (response.ok) {
                    loginKeyValueInput.value = data.login_key_value || key; // Save normalized key
                    userIdInput.value = data.user_id;
                    loginKeyTypeInput.value = data.login_key_type;

                    if (data.next_step === 'profile_selection') {
                        // Go to Step 2: Business Selection
                        renderBusinesses(data.profiles);
                        showMessage(data.message, 'warning');
                    } else if (data.next_step === 'verification') {
                        // Go directly to Step 3: Verification
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

        // Step 2A: Select Business (and trigger Role Selection if needed)
        async function handleBusinessSelection() {
            clearMessage();
            setLoading('select-business-button', true);

            if (!selectedBusiness) {
                showMessage('চালিয়ে যেতে দয়া করে একটি ব্যবসা নির্বাচন করুন।', 'warning');
                setLoading('select-business-button', false);
                return;
            }
            
            const payload = {
                user_id: userIdInput.value,
                login_key_value: loginKeyValueInput.value,
                login_key_type: loginKeyTypeInput.value,
                business_id: selectedBusiness.business_id,
                // Do NOT send role yet, let the backend decide if role selection is needed
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

                if (response.ok) {
                    if (data.next_step === 'role_selection') {
                        // Go to Step 2B: Role Selection
                        // Update the selectedBusiness object with more complete data if needed
                        selectedBusiness.roles = data.roles; 
                        selectedBusiness.business_name = data.business_name;
                        renderRoles(selectedBusiness);
                        showMessage(data.message, 'warning');
                    } else if (data.next_step === 'verification') {
                        // Go directly to Step 3: Verification (single role auto-selected)
                        setVerificationFields(data);
                    }
                } else {
                    showMessage(data.message || 'প্রোফাইল নির্বাচন ব্যর্থ হয়েছে।', 'error');
                }

            } catch (error) {
                showMessage('নেটওয়ার্ক ত্রুটি বা সার্ভার থেকে অপ্রত্যাশিত প্রতিক্রিয়া।', 'error');
                console.error('Error during selectProfile (Business):', error);
            } finally {
                setLoading('select-business-button', false);
            }
        }
        
        // Step 2B: Select Role (and proceed to Verification)
        async function handleRoleSelection() {
            clearMessage();
            setLoading('select-role-button', true);
            
            if (!selectedRole) {
                showMessage('চালিয়ে যেতে দয়া করে একটি ভূমিকা (Role) নির্বাচন করুন।', 'warning');
                setLoading('select-role-button', false);
                return;
            }
            
            // Now we have the final selection: Business ID and Role Name
            const payload = {
                user_id: userIdInput.value,
                login_key_value: loginKeyValueInput.value,
                login_key_type: loginKeyTypeInput.value,
                business_id: selectedBusiness.business_id, // Selected from step 2A
                role: selectedRole, // Selected from step 2B
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
                    showMessage(data.message || 'ভূমিকা নির্বাচন ব্যর্থ হয়েছে।', 'error');
                }

            } catch (error) {
                showMessage('নেটওয়ার্ক ত্রুটি বা সার্ভার থেকে অপ্রত্যাশিত প্রতিক্রিয়া।', 'error');
                console.error('Error during selectProfile (Role):', error);
            } finally {
                setLoading('select-role-button', false);
            }
        }


        // Step 3: Finalize Login
        async function handleFinalizeLogin() {
            clearMessage();
            setLoading('login-button', true);

            const payload = {
                // login_key uses the value from the hidden input, which is set in handleIdentifyUser
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
                        // Assuming data.redirect_url is provided by the controller
                        window.location.href = data.redirect_url; 
                    }, 1500);
                } else {
                    // Display error message
                    showMessage(data.message || 'লগইন ব্যর্থ হয়েছে।', 'error');
                }

            } catch (error) {
                showMessage('নেটওয়ার্ক ত্রুটি বা সার্ভার থেকে অপ্রত্যাশিত প্রতিক্রিয়া।', 'error');
                console.error('Error during finalizeLogin:', error);
            } finally {
                setLoading('login-button', false);
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
