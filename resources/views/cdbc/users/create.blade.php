<x-admin-layout>
    <x-slot name="page_title">
        {{ isset($user) ? 'Edit User' : 'Create User' }}
    </x-slot>

    <div class="cdbc-container cdbc-user-create">
        <h2 class="cdbc-title">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h2>

        <form id="user-form" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="cdbc-row">
                <div class="cdbc-col-left">
                    <div class="cdbc-card">
                        <h3 class="cdbc-card-title">User Information</h3>

                        <label>Name <span class="cdbc-required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Name">
                        @error('name')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror

                        <label>Email <span class="cdbc-required">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Email Address">
                        @error('email')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror

                        <label>Phone <span class="cdbc-required">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Primary Phone">
                        @error('phone')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror

                        <label>Secondary Phone</label>
                        <input type="text" name="secondary_phone" value="{{ old('secondary_phone', $user->secondary_phone ?? '') }}" placeholder="Secondary Phone">
                        @error('secondary_phone')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror

                        <label>Password {{ isset($user) ? '(Leave blank to keep current)' : '' }}</label>
                        <input type="password" name="password" placeholder="Password">
                        @error('password')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror

                        <label>Status</label>
                        <select name="status">
                            <option value="1" {{ (old('status', $user->status ?? '') == 1) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (old('status', $user->status ?? '') == 0) ? 'selected' : '' }}>Inactive</option>
                            <option value="2" {{ (old('status', $user->status ?? '') == 2) ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror
                        
                        {{-- Developer Access checkbox (commented out) it will be uncommented later --}}
                        {{--
                        <div class="cdbc-checkbox">
                            <input type="checkbox" name="is_developer" {{ old('is_developer', $user->is_developer ?? false) ? 'checked' : '' }}>
                            <label>Developer Access</label>
                        </div>
                        --}}
                    </div>
                </div>

                <div class="cdbc-col-right">
                    <div class="cdbc-card">
                        <h3 class="cdbc-card-title">User Profiles</h3>
                        
                        <div class="cdbc-card business-assignment-card role-section-hidden" id="role-section">
                            <h4 class="cdbc-card-subtitle">Role Assignment:</h4>

                            @php
                                // Get the assigned role ID if exists for this business
                                $currentRoleId = null;
                                if(isset($user) && isset($currentBusinessId)) {
                                    //$currentRole = $user->profiles()->with('roles')->where('business_id',$currentBusinessId)->first()?->roles->first();
                                    //$currentRole = $profile->roles()->wherePivot('business_id', $currentBusinessId)->first();
                                    $currentRoleId = $currentSelectedRoleId;// $currentRole ? $currentRole->id : null;
                                }
                               // dd($currentRoleId);
                                $selectedRoleId = old('role_id', $currentRoleId);
                            @endphp

                            <select name="role_id" id="role-select-option" data-selected-role-id="{{ $selectedRoleId}}">
                                <option value="">Select User Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $selectedRoleId == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="cdbc-error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{--
                            BUSINESS SELECTION LOGIC (ONLY FOR PRIME/SOFTWARE OWNER EMPLOYEE)
                            Tenant employees will skip this section and automatically be assigned to their own business.
                        --}}
                        @if ($isSoftwareOwnerEmployee)
                            <div class="cdbc-card business-assignment-card">
                                <h4 class="cdbc-card-subtitle">Assign User To:</h4>
                                @php
                                    $hasAnotherBusinessProfile = isset($user) && $user->profiles->contains(function($profile) use ($currentBusinessId) {
                                        return $profile->business_id != $currentBusinessId;
                                    });
                                    $radioValue = old('business_assignment_type', $hasAnotherBusinessProfile ? 'another' : 'own');
                                @endphp
                                
                                <div class="cdbc-checkbox">
                                    <input type="radio" id="own_business" name="business_assignment_type" value="own" {{ $radioValue == 'own' ? 'checked' : '' }}>
                                    <label for="own_business">
                                        Own Business: <strong>{{ $currentBusinessName }}</strong>
                                    </label>
                                </div>
                                <div class="cdbc-checkbox">
                                    <input type="radio" id="another_business" name="business_assignment_type" value="another" {{ $radioValue == 'another' ? 'checked' : '' }}>
                                    <label for="another_business">
                                        User for another business (Show Dropdown)
                                    </label>
                                </div>
                            </div>
                        @endif

                        {{-- General Profile Validation Errors --}}
                        @error('profiles')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror
                        @error('profiles.*.default_login')
                            <p class="cdbc-error-message">{{ $message }}</p>
                        @enderror
                        {{-- Client-side warning container --}}
                        <div id="duplicate-profile-warning-container"></div>
                        
                        <div id="profiles-container">
                            @php
                                $existingProfiles = isset($user) ? $user->profiles->map(function($profile) {
                                    return [
                                        'user_type_id' => $profile->user_type_id,
                                        'business_id' => $profile->business_id,
                                        'default_login' => $profile->default_login,
                                    ];
                                })->toArray() : [[]];
                                
                                $profiles = old('profiles', $existingProfiles);

                                $profiles = array_map(function($p) {
                                    return (array) $p;
                                }, $profiles);
                            @endphp

                            @foreach($profiles as $p)
                                @php
                                    $loopIndex = $loop->index;
                                    $currentProfileBusinessId = $p['business_id'] ?? null;
                                    // Check if this profile's business ID matches the current business ID (for initial setup)
                                    $isOwnBusinessProfile = $currentProfileBusinessId == $currentBusinessId;

                                    // Determine if dropdown should be shown initially based on edit context/old data
                                    $showDropdownInitially = ($radioValue == 'another' || (!$isOwnBusinessProfile && isset($user)));
                                    
                                    $hiddenInputName = $showDropdownInitially ? "profiles[{$loopIndex}][business_id]_disabled" : "profiles[{$loopIndex}][business_id]";
                                    $dropdownName = $showDropdownInitially ? "profiles[{$loopIndex}][business_id]" : "profiles[{$loopIndex}][business_id]_disabled";
                                @endphp
                                
                                <div class="cdbc-profile-row" data-index="{{ $loopIndex }}">
                                    <div class="cdbc-profile-selects">
                                        {{-- User Type Select --}}
                                        <select name="profiles[{{$loopIndex}}][user_type_id]" required>
                                            <option value="">Select User Type</option>
                                            @foreach($userTypes as $ut)
                                                @php
                                                    $current_user_type_id = $p['user_type_id'] ?? null;
                                                    $isSelected = ($current_user_type_id == $ut->id);
                                                @endphp
                                                <option value="{{ $ut->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $ut->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- User Type Error --}}
                                        @error("profiles.$loopIndex.user_type_id")
                                            <p class="cdbc-error-message">{{ $message }}</p>
                                        @enderror
                                        
                                        {{-- Conditional Business Field --}}
                                        @if ($hasBusinessAssigned)
                                            <div class="business-field-container" data-index="{{ $loopIndex }}">
                                                @if (!$isSoftwareOwnerEmployee)
                                                    {{-- Tenant Employee: Force assign to current business (Hidden Field) --}}
                                                    <input type="hidden" name="profiles[{{$loopIndex}}][business_id]" value="{{ $currentBusinessId }}">
                                                    <input type="text" value="{{ $currentBusinessName }}" disabled style="min-width:150px; background:#f0f0f0;">
                                                @else
                                                    {{-- Software Owner Employee: Dynamic Field (Dropdown or Hidden) --}}
                                                    
                                                    {{-- Default (Own Business) Hidden Field --}}
                                                    <input type="hidden"
                                                            name="{{ $hiddenInputName }}"
                                                            class="business-input-own business-input-{{ $loopIndex }}"
                                                            value="{{ $currentBusinessId }}"
                                                            {{ $showDropdownInitially ? 'disabled' : '' }}>
                                                    
                                                    {{-- Default (Own Business) Text Field --}}
                                                    <input type="text"
                                                            value="{{ $currentBusinessName }}"
                                                            class="business-text-own business-text-{{ $loopIndex }}"
                                                            disabled style="min-width:150px; background:#f0f0f0; display:{{ $showDropdownInitially ? 'none' : 'block' }};">

                                                    {{-- Dropdown for Another Business --}}
                                                    <select name="{{ $dropdownName }}"
                                                            class="business-dropdown business-dropdown-{{ $loopIndex }}"
                                                            style="display:{{ $showDropdownInitially ? 'block' : 'none' }}; min-width:150px;"
                                                            {{ $showDropdownInitially ? '' : 'disabled' }}>
                                                        <option value="">Select Business</option>
                                                        @foreach($businesses as $b)
                                                            @php
                                                                $isSelected = ($currentProfileBusinessId == $b->id);
                                                            @endphp
                                                            <option value="{{ $b->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                                {{ $b->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                            {{-- Business ID Error --}}
                                            @error("profiles.$loopIndex.business_id")
                                                <p class="cdbc-error-message">{{ $message }}</p>
                                            @enderror
                                        @endif
                                        
                                        {{-- Custom Duplication Error --}}
                                        @error("profiles.$loopIndex.duplicate")
                                            <p class="cdbc-error-message">{{ $message }}</p>
                                        @enderror
                                        
                                    </div>

                                    <div class="cdbc-profile-checkbox-row">
                                        <div class="cdbc-checkbox profile-checkbox">
                                            @php
                                                $isDefaultLogin = $p['default_login'] ?? false;
                                            @endphp
                                            <input type="checkbox" id="defaultLogin_{{$loopIndex}}" name="profiles[{{$loopIndex}}][default_login]" class="default-login" {{ $isDefaultLogin ? 'checked' : '' }}>
                                            <label for="defaultLogin_{{$loopIndex}}">Default Login</label>
                                        </div>
                                        <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">X Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="cdbc-btn cdbc-btn-primary" id="add-profile-btn">+ Add Profile</button>
                    
                        <hr/>
                        <div class="cdbc-btn-row" style="display: flex; justify-content: right">
                            <button type="submit" class="cdbc-btn cdbc-btn-success" style="line-height: 24px">{{ isset($user) ? 'Update User' : '+ Create User' }}</button>
                            <a href="{{ route('admin.users.index') }}" class="cdbc-btn cdbc-btn-secondary">Back</a>
                        </div>
                        
                    </div>
                </div>
            </div>
            {{-- Hidden field to signal to the controller whether the Software Owner employee chose 'Own Business' --}}
            @if ($isSoftwareOwnerEmployee)
                @php
                    $hiddenValue = $radioValue == 'another' ? '0' : '1';
                @endphp
                <input type="hidden" name="create_for_own_business" id="create_for_own_business" value="{{ $hiddenValue }}">
            @endif
        </form>
    </div>

    @push('css')
        <link rel="stylesheet" href="{{ asset('admin/css/cdbc-users.css') }}">
        <style>
            /* --- Container & Layout --- */
            .cdbc-container { padding:20px; max-width:1200px; margin:0 auto; }
            .cdbc-row { display:flex; gap:20px; flex-wrap:wrap; }
            .cdbc-col-left { flex:1 1 45%; min-width:300px; }
            .cdbc-col-right { flex:1 1 50%; min-width:300px; }

            /* --- Card --- */
            .cdbc-card { background:#fff; border:1px solid #ddd; border-radius:8px; padding:15px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin-bottom:20px; }
            .cdbc-card-title { font-size:18px; font-weight:600; margin-bottom:15px; }
            .cdbc-card-subtitle { font-size:16px; font-weight:500; margin-bottom:10px; color: #3498db; }
            .business-assignment-card { border: 1px dashed #3498db; }

            /* --- Inputs & Selects --- */
            input[type="text"], input[type="email"], input[type="password"], select {
                width:100%; padding:8px 10px; margin-bottom:12px; border-radius:4px; border:1px solid #ccc;
            }
            label { font-weight:500; margin-bottom:4px; display:block; }

            /* --- Checkbox & Radio --- */
            .cdbc-checkbox { display:flex; align-items:center; gap:6px; margin-bottom:12px; }
            .cdbc-checkbox input[type="checkbox"], .cdbc-checkbox input[type="radio"] { width:18px; height:18px; cursor:pointer; margin: 0; padding: 0; }
            .cdbc-profile-checkbox-row .cdbc-checkbox { margin-bottom: 0; }

            /* --- Profile Row --- */
            .cdbc-profile-row { border:1px solid #ddd; border-radius:6px; padding:10px; margin-bottom:10px; background:#fafafa; transition: border 0.3s; }
            .cdbc-profile-row.duplicate { border: 2px solid #e74c3c; } /* Duplication highlight */

            .cdbc-profile-checkbox-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
            .cdbc-profile-selects { display:flex; gap:10px; flex-wrap:wrap; align-items: center; }
            .cdbc-profile-selects select, .business-field-container input[type="text"] { min-width:150px; padding:6px 8px; border-radius:4px; border:1px solid #ccc; margin-bottom: 0; }
            .business-field-container { margin-bottom: 12px; }

            /* --- Buttons --- */
            .cdbc-btn { padding:6px 12px; border-radius:4px; border:none; cursor:pointer; font-weight:500; }
            .cdbc-btn-primary { background:#3498db; color:#fff; }
            .cdbc-btn-primary:hover { opacity:0.85; }
            .cdbc-btn-success { background:#27ae60; color:#fff; }
            .cdbc-btn-success:hover { opacity:0.85; }
            .cdbc-btn-secondary { background:#95a5a6; color:#fff; text-decoration:none; display:inline-block; line-height:24px; padding:6px 12px; margin-left:10px; }
            .cdbc-btn-danger { background:#e74c3c; color:#fff; }
            .cdbc-btn-danger:hover { opacity:0.85; }

            /* --- Bottom Row --- */
            .cdbc-btn-row { margin-top:15px; }

            /* --- Title & Required --- */
            .cdbc-title { font-size:24px; font-weight:600; margin-bottom:20px; }
            .cdbc-required { color:#e74c3c; }
            
            /* --- Validation Error Message --- */
            .cdbc-error-message {
                color: #e74c3c;
                font-size: 13px;
                margin-top: -10px; 
                margin-bottom: 12px;
                width: 100%;
            }
            .cdbc-profile-selects > .cdbc-error-message {
                order: 10; /* Push error message down */
            }

            .role-section-hidden { display: none; border: 1px solid #ddd}
        </style>
    @endpush

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