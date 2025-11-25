<x-admin-layout>

    <div class="container">
        {{-- ফর্ম অ্যাকশন: এডিট হলে 'update', না হলে 'store' --}}
        <form id="user-form" 
            action="{{ isset($user) ? route('user.update', $user) : route('user.store') }}" 
            method="POST">
            @csrf
            {{-- এডিট হলে PUT মেথড ব্যবহার করা --}}
            @if(isset($user))
                @method('PUT')
            @endif
            
            <h2 class="mb-4">{{ isset($user) ? 'ইউজার এডিট করুন' : 'নতুন ইউজার তৈরি করুন' }}</h2>
            
            {{-- অন্যান্য ইউজার ইনপুট ফিল্ড (name, phone, email, status) এখানে থাকবে --}}

            {{-- পাসওয়ার্ড ফিল্ড: এডিটের জন্য অপশনাল --}}
            <div class="form-group">
                <label for="password">পাসওয়ার্ড</label>
                <input type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">@if(isset($user)) পাসওয়ার্ড পরিবর্তন না করতে চাইলে খালি রাখুন। @endif</small>
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            {{-- Repeat for password_confirmation --}}

            
            {{-- টেন্যান্সি রেডিও বাটন সেকশন (শুধুমাত্র প্রাইম ইউজারের জন্য) --}}
            @if ($isSoftwareOwnerEmployee)
                <div class="form-group mb-3 p-3 border rounded">
                    <label>ইউজার তৈরি:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="create_for_own_business" id="own_business" value="1" 
                            {{ old('create_for_own_business', $is_own_business_creation_mode ?? '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="own_business">আমার ব্যবসার জন্য ({{ $currentBusinessName }})</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="create_for_own_business" id="another_business" value="0" 
                            {{ old('create_for_own_business', $is_own_business_creation_mode ?? '1') == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="another_business">অন্য ব্যবসার জন্য</label>
                    </div>
                    
                    {{-- JS লজিকের জন্য সিগন্যাল হিডেন ফিল্ড --}}
                    <input type="hidden" id="create_for_own_business" name="create_for_own_business" value="{{ old('create_for_own_business', $is_own_business_creation_mode ?? '1') }}">
                </div>
            @endif

            {{-- ২. রোল সেকশন (শুধুমাত্র Own Business মোডের জন্য) --}}
            <div id="role-section" class="form-group" style="display: {{ ($isSoftwareOwnerEmployee && !($is_own_business_creation_mode ?? true)) ? 'none' : 'block' }}">
                <label for="role_id">রোল অ্যাসাইন করুন ({{ $currentBusinessName }} এর জন্য)</label>
                <select name="role_id" id="role-select-option" class="form-control" data-selected-role-id="{{ $selectedRoleId ?? '' }}">
                    <option value="">কোনো রোল নয়</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" 
                            {{ old('role_id', $selectedRoleId) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- ৩. প্রোফাইল মাল্টি-সিলেকশন --}}
            <h3>প্রোফাইল সেটআপ</h3>
            <div id="duplicate-profile-warning-container"></div>
            <div id="profiles-container">
                @foreach (old('profiles', $profiles) as $index => $profileData)
                    @include('user._profile_row', ['index' => $index, 'profileData' => $profileData])
                @endforeach
            </div>
            
            <button type="button" id="add-profile-btn" class="btn btn-secondary mt-3">+ আরও প্রোফাইল যোগ করুন</button>
            
            <button type="submit" class="btn btn-primary mt-4">আপডেট ইউজার</button>
        </form>
    </div>

    @push('script')
        <script>
            let profileCount = {{ count($profiles) - 1 }}; // Start count based on existing profiles
            const userTypes = @json($userTypes);
            const businesses = @json($businesses);
            const isSoftwareOwnerEmployee = {{ $isSoftwareOwnerEmployee ? 'true' : 'false' }};
            const currentBusinessId = "{{ $currentBusinessId }}";
            const currentBusinessName = "{{ $currentBusinessName }}";
            const hasBusinessAssigned = {{ $hasBusinessAssigned ? 'true' : 'false' }};


            // Function to handle the visibility toggle for business fields (only for Software Owner Employee)
            function updateBusinessFieldVisibility(profileIndex, isOwnBusinessSelected) {
                if (!isSoftwareOwnerEmployee || !hasBusinessAssigned) return;

                const dropdown = document.querySelector(`.business-dropdown-${profileIndex}`);
                const inputOwn = document.querySelector(`.business-input-own.business-input-${profileIndex}`);
                const textOwn = document.querySelector(`.business-text-own.business-text-${profileIndex}`);
                
                if (!dropdown || !inputOwn || !textOwn) return;
                
                // Base name for the input field
                const baseName = `profiles[${profileIndex}][business_id]`;

                if (isOwnBusinessSelected) {
                    // Show Own Business (Hidden Input + Disabled Text), Hide Dropdown
                    dropdown.style.display = 'none';
                    dropdown.disabled = true;
                    dropdown.name = `${baseName}_disabled`; // Deactivate dropdown name

                    inputOwn.disabled = false;
                    inputOwn.name = baseName; // Activate hidden input
                    
                    textOwn.style.display = 'block';
                } else {
                    // Show Dropdown, Hide Own Business Fields
                    dropdown.style.display = 'block';
                    dropdown.disabled = false;
                    dropdown.name = baseName; // Activate dropdown name

                    inputOwn.disabled = true;
                    inputOwn.name = `${baseName}_disabled`; // Deactivate hidden input name
                    
                    textOwn.style.display = 'none';
                }
            }
            
            // Function to check for duplicate profiles across all rows
            function checkForDuplicateProfiles() {
                const profilesContainer = document.getElementById('profiles-container');
                const profileRows = profilesContainer.querySelectorAll('.cdbc-profile-row');
                const profilesMap = new Map();
                let isDuplicate = false;
                
                // Clear all previous highlight classes
                profileRows.forEach(row => row.classList.remove('duplicate'));

                profileRows.forEach(row => {
                    const index = row.dataset.index;
                    const userTypeSelect = row.querySelector(`select[name="profiles[${index}][user_type_id]"]`);
                    let businessId = null;
                    
                    // 1. Determine the actual business ID for this profile row
                    if (!hasBusinessAssigned) {
                        return; // Skip check if no business can be assigned
                    } else if (!isSoftwareOwnerEmployee || document.getElementById('own_business')?.checked) {
                        // Tenant or Prime creating for Own Business: Use currentBusinessId
                        businessId = currentBusinessId;
                    } else {
                        // Prime creating for Another Business: Use dropdown value (or hidden input if used)
                        const activeInput = row.querySelector(`select[name="profiles[${index}][business_id]"]`);
                        if(activeInput) {
                            businessId = activeInput.value;
                        }
                    }

                    const userTypeId = userTypeSelect ? userTypeSelect.value : null;
                    
                    // 2. Perform Duplication Check
                    if (userTypeId && businessId) {
                        const key = `${userTypeId}-${businessId}`;
                        if (profilesMap.has(key)) {
                            isDuplicate = true;
                            row.classList.add('duplicate');
                            profilesMap.get(key).classList.add('duplicate');
                        } else {
                            profilesMap.set(key, row);
                        }
                    }
                });
                
                const warningContainer = document.getElementById('duplicate-profile-warning-container');
                let duplicateMessage = document.getElementById('duplicate-profile-message');

                if (isDuplicate) {
                    if (!duplicateMessage) {
                        duplicateMessage = document.createElement('p');
                        duplicateMessage.id = 'duplicate-profile-message';
                        duplicateMessage.className = 'cdbc-error-message';
                        duplicateMessage.textContent = 'সতর্কতা: একই User Type এবং Business-এর প্রোফাইল ডুপ্লিকেট করা হয়েছে।';
                        warningContainer.appendChild(duplicateMessage);
                    }
                } else {
                    if (duplicateMessage) duplicateMessage.remove();
                }

                return isDuplicate; 
            }
            
            // Core logic for creating a new profile row
            function createProfileRow(profile = {}) {
                profileCount++;
                const index = profileCount;
                const row = document.createElement('div');
                row.className = 'cdbc-profile-row';
                row.setAttribute('data-index', index); // Set data-index for easy reference

                let businessHtml = '';
                if (hasBusinessAssigned) {
                    // Determine initial display based on radio buttons
                    const isOwnBusinessSelected = document.getElementById('own_business')?.checked ?? true; // Default true for new row
                    const hiddenInputName = isOwnBusinessSelected ? `profiles[${index}][business_id]` : `profiles[${index}][business_id]_disabled`;
                    const dropdownName = isOwnBusinessSelected ? `profiles[${index}][business_id]_disabled` : `profiles[${index}][business_id]`;
                    
                    if (!isSoftwareOwnerEmployee) {
                        // Tenant: Fixed to own business
                        businessHtml = `
                            <div class="business-field-container" data-index="${index}">
                                <input type="hidden" name="profiles[${index}][business_id]" value="${currentBusinessId}">
                                <input type="text" value="${currentBusinessName}" disabled style="min-width:150px; background:#f0f0f0;">
                            </div>
                        `;
                    } else {
                        // Software Owner Employee: Dynamic selection.
                        businessHtml = `
                            <div class="business-field-container" data-index="${index}">
                                <input type="hidden"
                                        name="${hiddenInputName}"
                                        class="business-input-own business-input-${index}"
                                        value="${currentBusinessId}"
                                        ${isOwnBusinessSelected ? '' : 'disabled'}>
                                <input type="text"
                                        value="${currentBusinessName}"
                                        class="business-text-own business-text-${index}"
                                        disabled style="min-width:150px; background:#f0f0f0; display:${isOwnBusinessSelected ? 'block' : 'none'};">
                                        
                                <select name="${dropdownName}"
                                        class="business-dropdown business-dropdown-${index}"
                                        style="display:${isOwnBusinessSelected ? 'none' : 'block'}; min-width:150px;"
                                        ${isOwnBusinessSelected ? 'disabled' : ''}>
                                    <option value="">Select Business</option>
                                    ${businesses.map(b => `<option value="${b.id}">${b.name}</option>`).join('')}
                                </select>
                            </div>
                        `;
                    }
                }

                row.innerHTML = `
                    <div class="cdbc-profile-selects">
                        <select name="profiles[${index}][user_type_id]" required>
                            <option value="">Select User Type</option>
                            ${userTypes.map(u => `<option value="${u.id}">${u.display_name}</option>`).join('')}
                        </select>
                        ${businessHtml}
                    </div>

                    <div class="cdbc-profile-checkbox-row">
                        <div class="cdbc-checkbox profile-checkbox">
                            <input type="checkbox" id="defaultLogin_${index}" name="profiles[${index}][default_login]" class="default-login">
                            <label for="defaultLogin_${index}">Default Login</label>
                        </div>
                        <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">X Remove</button>
                    </div>
                `;

                document.getElementById('profiles-container').appendChild(row);
                
                // Attach event listeners and run check
                attachProfileRowListeners(row, index);
                checkForDuplicateProfiles();
            }
            
            // Function to attach listeners to profile rows
            function attachProfileRowListeners(row, index) {
                // Default login logic
                const checkbox = row.querySelector('.default-login');
                checkbox.addEventListener('change', function() {
                    if(this.checked){
                        document.querySelectorAll('.default-login').forEach(cb => {
                            if(cb !== this) cb.checked = false;
                        });
                    }
                });

                // Remove button
                const removeBtn = row.querySelector('.remove-profile');
                removeBtn.addEventListener('click', function() {
                    row.remove();
                    checkForDuplicateProfiles(); // Check after removal
                });

                // Duplication check listeners
                const userTypeSelect = row.querySelector('select[name*="[user_type_id]"]');
                if (userTypeSelect) userTypeSelect.addEventListener('change', checkForDuplicateProfiles);

                const businessDropdown = row.querySelector('.business-dropdown');
                if (businessDropdown) businessDropdown.addEventListener('change', checkForDuplicateProfiles);
            }

            // --- Main Script Execution ---

            // 1. Setup Add Profile Button
            document.getElementById('add-profile-btn').addEventListener('click', function() {
                createProfileRow();
            });

            // 2. Initialize Existing Profiles and attach listeners
            document.querySelectorAll('.cdbc-profile-row').forEach(row => {
                // Use the data-index attribute set in PHP or loop index
                const index = row.dataset.index || profileCount;
                attachProfileRowListeners(row, index);
            });


            // 3. Tenancy Logic (ONLY for Software Owner Employee)
            if (isSoftwareOwnerEmployee) {
                const ownBusinessRadio = document.getElementById('own_business');
                const anotherBusinessRadio = document.getElementById('another_business');
                const profilesContainer = document.getElementById('profiles-container');
                const ownBusinessHiddenInput = document.getElementById('create_for_own_business');
                const roleSection = document.getElementById('role-section');
                const roleSelectOption = document.getElementById('role-select-option');

                const initialSelectedRoleId = roleSelectOption.dataset.selectedRoleId;

                function handleBusinessAssignmentChange() {
                    const isOwnSelected = ownBusinessRadio.checked;

                    // Role Section Visibility Logic
                    roleSection.style.display = isOwnSelected ? 'block' : 'none';
                    if(isOwnSelected){
                        if (initialSelectedRoleId) {
                        console.log(initialSelectedRoleId);
                        roleSelectOption.value = initialSelectedRoleId; 
                        console.log('Role Select Value Set to:', roleSelectOption.value);
                    }
                    } else {
                        roleSelectOption.value = ''; // Clear role selection
                    }

                    /* if(isOwnSelected){
                        roleSelectOption.data-selected-role-id
                    }
                    if(!isOwnSelected) {
                        roleSelectOption.value = ''; // Clear role if switching to another business
                    } */
                    
            
                    // Update controller signal hidden field
                    ownBusinessHiddenInput.value = isOwnSelected ? '1' : '0';

                    // Update all existing profile rows (enable/disable dropdown/hidden field)
                    profilesContainer.querySelectorAll('.cdbc-profile-row').forEach(row => {
                        const index = row.dataset.index;
                        updateBusinessFieldVisibility(index, isOwnSelected);
                    });
                    
                    checkForDuplicateProfiles(); // Run check after business field state changes
                }
                
                // Attach listeners to radio buttons
                ownBusinessRadio.addEventListener('change', handleBusinessAssignmentChange);
                anotherBusinessRadio.addEventListener('change', handleBusinessAssignmentChange);

                // Initial run to set the correct state on page load/edit
                setTimeout(() => {
                    handleBusinessAssignmentChange();
                    // Initial one default login check (ensure only one is checked)
                    const checkedBoxes = document.querySelectorAll('.default-login:checked');
                    if(checkedBoxes.length > 1){
                        checkedBoxes.forEach((cb, i) => { if(i > 0) cb.checked = false; });
                    }
                }, 10);
            }

            // 4. Client-side Duplication & Default Login Check before Submit
            document.getElementById('user-form').addEventListener('submit', function(e) {
                // Check if any profile has no default login selected
                const defaultLoginCount = document.querySelectorAll('.default-login:checked').length;
                
                if (defaultLoginCount !== 1) {
                    alert('Before submitting the form, please select a default login for at least one profile.');
                    e.preventDefault();
                    return;
                }
                
                if (checkForDuplicateProfiles()) {
                    alert('Before submitting the form, please correct any duplicate profiles.');
                    e.preventDefault();
                }
            });
        </script>
    @endpush
</x-admin-layout>

{{-- আপনার _profile_row.blade.php ফাইলটি নিচে দেওয়া হলো --}}