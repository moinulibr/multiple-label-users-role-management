<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন - মাল্টি-টেন্যান্ট সিস্টেম</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .card {
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        @media (max-width: 640px) {
            .responsive-padding {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center responsive-padding p-4 sm:p-6 md:p-8">
        <div class="w-full max-w-md p-6 sm:p-8 space-y-6 bg-white rounded-xl card transition-all duration-300">
            
            <header class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    স্বাগতম
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    ইমেল বা ফোন নম্বর দিয়ে শুরু করুন
                </p>
            </header>

            <!-- Error/Success Message Area -->
            <div id="message-area" class="p-3 rounded-md text-sm transition-all duration-300 hidden" role="alert"></div>

            <form id="login-form" class="space-y-6">
                @csrf
                
                <!-- Hidden input to store step 2 method (password/otp) -->
                <input type="hidden" id="step_2_method_input" name="step_2_method" value="password">
                
                <!-- Step 1: Email or Phone -->
                <div id="step-1">
                    <label for="login_key" class="block text-sm font-medium text-gray-700 mb-2">
                        ইমেল বা ফোন নম্বর
                    </label>
                    <input id="login_key" name="login_key" type="text" autocomplete="username" 
                        class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150"
                        placeholder="আপনার ইমেল বা ফোন নম্বর দিন">
                    
                    <button id="next-button" type="submit" disabled
                        class="w-full mt-6 flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out disabled:bg-indigo-400 disabled:cursor-not-allowed">
                        পরবর্তী
                    </button>
                </div>

                <!-- Step 2: Password / OTP (Initially hidden) -->
                <div id="step-2" class="hidden">
                    <div class="flex justify-between items-center mb-2">
                        <!-- 💡 ডাইনামিক লেবেল -->
                        <label id="password-label" for="password" class="block text-sm font-medium text-gray-700">
                            পাসওয়ার্ড
                        </label>
                        <button type="button" id="change-key-button" class="text-xs text-indigo-600 hover:text-indigo-800 transition duration-150">
                            পরিবর্তন করুন (<span id="display-key" class="font-semibold text-gray-600"></span>)
                        </button>
                    </div>
                    
                    <!-- 💡 ডাইনামিক ইনপুট ফিক্সড -->
                    <input id="password" name="password" type="password" autocomplete="current-password" 
                        class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150"
                        placeholder="আপনার পাসওয়ার্ড প্রবেশ করান">
                    
                    <button id="login-button" type="submit" disabled
                        class="w-full mt-6 flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out disabled:bg-green-400 disabled:cursor-not-allowed">
                        লগইন
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('login-form');
        const loginKeyInput = document.getElementById('login_key');
        const passwordInput = document.getElementById('password');
        const nextButton = document.getElementById('next-button');
        const loginButton = document.getElementById('login-button');
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const messageArea = document.getElementById('message-area');
        const changeKeyButton = document.getElementById('change-key-button');
        const displayKeySpan = document.getElementById('display-key');
        const passwordLabel = document.getElementById('password-label');
        const step2MethodInput = document.getElementById('step_2_method_input');

        let currentStep = 1;
        let lastResponse = null; // response is not defined ত্রুটি ফিক্স করার জন্য
        
        // 💡 রাউট নাম
        const IDENTIFY_ROUTE = '{{ route('login.identify') }}'; 
        const FINALIZE_ROUTE = '{{ route('login.finalize') }}'; 

        // --- Helper Functions ---

        /** বাটন স্টেট আপডেট করা */
        function updateButtonState() {
            if (currentStep === 1) {
                nextButton.disabled = loginKeyInput.value.trim() === '';
            } else if (currentStep === 2) {
                loginButton.disabled = passwordInput.value.trim() === ''; 
            }
        }

        /** লোডিং স্টেট সেট করা */
        function setLoading(button, isLoading, defaultText) {
            nextButton.disabled = loginButton.disabled = isLoading;
            button.textContent = isLoading ? 'অপেক্ষা করুন...' : defaultText;
            
            // লোডিং অবস্থায় ইনপুট এবং পরিবর্তন বাটন ডিসেবল করা
            loginKeyInput.disabled = isLoading;
            passwordInput.disabled = isLoading;
            changeKeyButton.disabled = isLoading;
        }

        /** মেসেজ ডিসপ্লে করা */
        function displayMessage(message, type = 'error') {
            messageArea.textContent = message;
            messageArea.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
            
            if (type === 'error') {
                messageArea.classList.add('bg-red-100', 'text-red-700');
            } else {
                messageArea.classList.add('bg-green-100', 'text-green-700');
            }
            messageArea.classList.remove('hidden');
        }

        /** মেসেজ লুকানো */
        function hideMessage() {
            messageArea.classList.add('hidden');
        }

        /** ডাইনামিক প্লেসহোল্ডার এবং লেবেল সেট করা */
        function setDynamicFields(step2Method) {
            const isPassword = step2Method === 'password';
            
            // লেবেল পরিবর্তন: OTP বা পাসওয়ার্ড
            passwordLabel.textContent = isPassword ? 'পাসওয়ার্ড' : 'OTP';
            
            // প্লেসহোল্ডার পরিবর্তন
            passwordInput.placeholder = isPassword ? 'আপনার পাসওয়ার্ড প্রবেশ করান' : 'আপনার OTP প্রবেশ করান';

            // ইনপুট টাইপ সেট করা
            passwordInput.type = isPassword ? 'password' : 'text';
            passwordInput.setAttribute('autocomplete', isPassword ? 'current-password' : 'one-time-code');
            passwordInput.setAttribute('inputmode', isPassword ? 'text' : 'numeric');
        }

        // --- Event Listeners ---
        
        loginKeyInput.addEventListener('input', updateButtonState);
        passwordInput.addEventListener('input', updateButtonState);
        window.addEventListener('load', updateButtonState);
        
        // পরিবর্তন করুন বাটনে ক্লিক (স্টেপ ১ এ ফিরে যাওয়া)
        changeKeyButton.addEventListener('click', () => {
            if (loginKeyInput.disabled) return; 

            currentStep = 1;
            step1.classList.remove('hidden');
            step2.classList.add('hidden');
            passwordInput.value = ''; 
            loginKeyInput.focus();
            hideMessage();
            updateButtonState();

            // 💡 স্টেপ ১ এর ফিল্ড এনেবল করা
            loginKeyInput.disabled = false;
        });


        // --- Form Submission Logic ---

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideMessage();

            const formData = new FormData(form);
            let url, button, defaultText;
            let currentButton;
            lastResponse = null; // রিসেট করা হলো
            
            if (currentStep === 1) {
                url = IDENTIFY_ROUTE;
                button = nextButton;
                defaultText = 'পরবর্তী';
                currentButton = nextButton;
            } else {
                url = FINALIZE_ROUTE;
                button = loginButton;
                defaultText = 'লগইন';
                currentButton = loginButton;
            }

            setLoading(currentButton, true, defaultText);
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData,
                });

                lastResponse = response; // response সংরক্ষণ করা হলো
                let data;
                const contentType = response.headers.get("content-type");
                
                // 404 (Not Found) হ্যান্ডলিং
                if (response.status === 404) {
                    throw new Error('404: Route Not Found. আপনার সার্ভার রুট কনফিগারেশন চেক করুন।');
                }
                
                if (response.status === 302 || !contentType || !contentType.includes("application/json")) {
                    throw new Error('সার্ভার থেকে অপ্রত্যাশিত রেসপন্স এসেছে। পেজ রিফ্রেশ করে চেষ্টা করুন।');
                }
                
                data = await response.json();
                
                if (response.ok) {
                    // সফল রেসপন্স
                    if (currentStep === 1) {
                        // স্টেপ ১ সফল: স্টেপ ২ তে যাও
                        currentStep = 2;
                        step1.classList.add('hidden');
                        step2.classList.remove('hidden');
                        
                        // ডাইনামিক ফিল্ড এবং স্টেপ ২ মেথড সেট করা
                        setDynamicFields(data.step_2_method); 
                        step2MethodInput.value = data.step_2_method; // Hidden ইনপুটে সেভ করা
                        
                        // 💡 স্টেপ ১ ইনপুট ডিসেবল করা হলো
                        loginKeyInput.disabled = true;
                        
                        // ডেটাবেস থেকে আসা নর্মাল ভ্যালুটি ডিসপ্লেতে দেখানো হলো
                        displayKeySpan.textContent = data.login_key_value; 
                        
                        passwordInput.value = ''; 
                        passwordInput.focus();
                        displayMessage(data.message, 'success');
                        
                        setLoading(currentButton, false, defaultText); // লোডিং স্টেট রিমুভ করা
                        updateButtonState();

                    } else {
                        // স্টেপ ২ সফল: লগইন সম্পন্ন, রিডাইরেক্ট করো
                        displayMessage(data.message, 'success');
                        
                        // রিডাইরেক্টের জন্য লোডিং স্টেট রেখে দেওয়া হলো
                        setTimeout(() => {
                            window.location.href = data.redirect_url || '{{ route('dashboard') }}'; 
                        }, 1000); 
                        
                        return; // ফিনিশড
                    }
                } else {
                    // সার্ভার সাইড এরর (4xx/5xx)
                    let errorMsg = data.message || 'একটি অপ্রত্যাশিত ত্রুটি ঘটেছে।';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors)[0][0]; 
                    }
                    displayMessage(errorMsg, 'error');
                }

            } catch (error) {
                console.error('Login Error:', error);
                displayMessage(error.message || 'সার্ভারের সাথে সংযোগ স্থাপন করা যায়নি।', 'error');
            } finally {
                // 💡 response is not defined ত্রুটি ফিক্স করা হলো: lastResponse ব্যবহার করে
                if (currentStep === 1 || !lastResponse || !lastResponse.ok) {
                    setLoading(currentButton, false, defaultText);
                    updateButtonState();
                }
            }
        });
    </script>
</body>
</html>
