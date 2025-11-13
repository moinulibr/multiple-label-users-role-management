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

                        <label>Email <span class="cdbc-required">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Email Address">

                        <label>Phone <span class="cdbc-required">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Primary Phone">

                        <label>Secondary Phone</label>
                        <input type="text" name="secondary_phone" value="{{ old('secondary_phone', $user->secondary_phone ?? '') }}" placeholder="Secondary Phone">

                        <label>Password {{ isset($user) ? '(Leave blank to keep current)' : '' }}</label>
                        <input type="password" name="password" placeholder="Password">

                        <label>Status</label>
                        <select name="status">
                            <option value="1" {{ (old('status', $user->status ?? '') == 1) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (old('status', $user->status ?? '') == 0) ? 'selected' : '' }}>Inactive</option>
                            <option value="2" {{ (old('status', $user->status ?? '') == 2) ? 'selected' : '' }}>Suspended</option>
                        </select>
                        
                        {{-- Developer Access checkbox (commented out) --}}
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
                        
                        {{-- 
                            BUSINESS SELECTION LOGIC (ONLY FOR PRIME/SOFTWARE OWNER EMPLOYEE) 
                            Tenant employees will skip this section and automatically be assigned to their own business.
                        --}}
                        @if ($isSoftwareOwnerEmployee)
                            <div class="cdbc-card business-assignment-card">
                                <h4 class="cdbc-card-subtitle">Assign User To:</h4>
                                @php
                                    // এডিটের সময় যদি ইউজারের কোনো প্রোফাইল অন্য বিজনেসের হয়ে থাকে
                                    $hasAnotherBusinessProfile = isset($user) && $user->profiles->contains(function($profile) use ($currentBusinessId) {
                                        return $profile->business_id != $currentBusinessId;
                                    });
                                    // old() অথবা এডিটের ভিত্তিতে রেডিও বাটন চেক করা
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

                        <div id="profiles-container">
                            @php
                                // বিদ্যমান প্রোফাইল ডেটা বা পুরানো ডেটা সঠিকভাবে কনভার্ট করে লোড করা
                                $existingProfiles = isset($user) ? $user->profiles->map(function($profile) {
                                    return [
                                        'user_type_id' => $profile->user_type_id,
                                        'business_id' => $profile->business_id,
                                        'default_login' => $profile->default_login,
                                    ];
                                })->toArray() : [[]]; 
                                
                                $profiles = old('profiles', $existingProfiles);

                                // নিশ্চিত করা যে প্রতিটি প্রোফাইল একটি অ্যাসোসিয়েটিভ অ্যারে, অবজেক্ট নয়
                                $profiles = array_map(function($p) {
                                    return (array) $p;
                                }, $profiles);
                            @endphp

                            @foreach($profiles as $p)
                                <div class="cdbc-profile-row">
                                    <div class="cdbc-profile-selects">
                                        {{-- User Type Select --}}
                                        <select name="profiles[{{$loop->index}}][user_type_id]" required>
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
                                        
                                        {{-- Conditional Business Field --}}
                                        @if ($hasBusinessAssigned)
                                            <div class="business-field-container" data-index="{{ $loop->index }}">
                                                @if (!$isSoftwareOwnerEmployee)
                                                    {{-- Tenant Employee: Force assign to current business (Hidden Field) --}}
                                                    <input type="hidden" name="profiles[{{$loop->index}}][business_id]" value="{{ $currentBusinessId }}">
                                                    <input type="text" value="{{ $currentBusinessName }}" disabled style="min-width:150px; background:#f0f0f0;">
                                                @else
                                                    @php
                                                        $current_business_id = $p['business_id'] ?? null;
                                                        // যদি current_business_id থাকে এবং তা বর্তমান বিজনেসের না হয়, তবে ড্রপডাউন দেখাবে (Edit Mode এর জন্য)
                                                        $showDropdownInitially = isset($user) && $current_business_id && $current_business_id != $currentBusinessId;
                                                        
                                                        // তবে যদি old() ডেটা থেকে আসে, তখন রেডিও বাটনের ভ্যালু 'another' হলে ড্রপডাউন দেখাবে
                                                        if (!isset($user) && old('business_assignment_type', 'own') == 'another') {
                                                            $showDropdownInitially = true;
                                                        }

                                                        $hiddenInputName = $showDropdownInitially ? "profiles[{$loop->index}][business_id]_disabled" : "profiles[{$loop->index}][business_id]";
                                                        $dropdownName = $showDropdownInitially ? "profiles[{$loop->index}][business_id]" : "profiles[{$loop->index}][business_id]_disabled";
                                                        
                                                    @endphp
                                                    
                                                    {{-- Software Owner Employee: Dynamic Field (Dropdown or Hidden) --}}
                                                    
                                                    {{-- Default (Own Business) Hidden Field --}}
                                                    <input type="hidden" 
                                                            name="{{ $hiddenInputName }}" 
                                                            class="business-input-own business-input-{{ $loop->index }}" 
                                                            value="{{ $currentBusinessId }}"
                                                            {{ $showDropdownInitially ? 'disabled' : '' }}>
                                                    
                                                    {{-- Default (Own Business) Text Field --}}
                                                    <input type="text" 
                                                            value="{{ $currentBusinessName }}" 
                                                            class="business-text-own business-text-{{ $loop->index }}" 
                                                            disabled style="min-width:150px; background:#f0f0f0; display:{{ $showDropdownInitially ? 'none' : 'block' }};">

                                                    {{-- Dropdown for Another Business --}}
                                                    <select name="{{ $dropdownName }}" 
                                                            class="business-dropdown business-dropdown-{{ $loop->index }}" 
                                                            style="display:{{ $showDropdownInitially ? 'block' : 'none' }}; min-width:150px;"
                                                            {{ $showDropdownInitially ? '' : 'disabled' }}>
                                                        <option value="">Select Business</option>
                                                        @foreach($businesses as $b)
                                                            @php
                                                                $isSelected = ($current_business_id == $b->id);
                                                            @endphp
                                                            <option value="{{ $b->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                                {{ $b->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                        @endif
                                        
                                    </div>

                                    <div class="cdbc-profile-checkbox-row">
                                        <div class="cdbc-checkbox profile-checkbox">
                                            @php
                                                $isDefaultLogin = $p['default_login'] ?? false;
                                            @endphp
                                            <input type="checkbox" name="profiles[{{$loop->index}}][default_login]" class="default-login" {{ $isDefaultLogin ? 'checked' : '' }}>
                                            <label>Default Login</label>
                                        </div>
                                        <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">X Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="cdbc-btn cdbc-btn-primary" id="add-profile-btn">+ Add Profile</button>
                    </div>
                </div>
            </div>

            <div class="cdbc-btn-row">
                <button type="submit" class="cdbc-btn cdbc-btn-success">{{ isset($user) ? 'Update User' : 'Create User' }}</button>
                <a href="{{ route('admin.users.index') }}" class="cdbc-btn cdbc-btn-secondary">Back</a>
            </div>
            
            {{-- Hidden field to signal to the controller whether the Software Owner employee chose 'Own Business' --}}
            @if ($isSoftwareOwnerEmployee)
                @php
                    // যদি 'another' সিলেক্ট করা থাকে, তবে ডিফল্ট ভ্যালু 0 হবে, অন্যথায় 1
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
            .cdbc-profile-row { border:1px solid #ddd; border-radius:6px; padding:10px; margin-bottom:10px; background:#fafafa; }
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
        </style>
    @endpush

    @push('script')
        <script>
            // profileCount শুরু হবে বিদ্যমান প্রোফাইলের সংখ্যা থেকে, এবং add-এর সময় এটি বাড়বে
            let profileCount = {{ count($profiles) }}; 
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

                    inputOwn.type = 'hidden'; // Make sure the hidden field is active
                    inputOwn.disabled = false;
                    inputOwn.name = baseName; // Activate hidden input
                    
                    textOwn.style.display = 'block';
                } else {
                    // Show Dropdown, Hide Own Business Fields
                    dropdown.style.display = 'block';
                    dropdown.disabled = false;
                    dropdown.name = baseName; // Activate dropdown name

                    inputOwn.type = 'text'; // Deactivate hidden input (turn it into a non-submittable field)
                    inputOwn.disabled = true;
                    inputOwn.name = `${baseName}_disabled`; // Deactivate hidden input name
                    
                    textOwn.style.display = 'none';
                }
            }
            
            // Core logic for creating a new profile row
            function createProfileRow(profile = {}) {
                profileCount++;
                const index = profileCount;
                const row = document.createElement('div');
                row.className = 'cdbc-profile-row';

                let businessHtml = '';
                if (hasBusinessAssigned) {
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
                        // Default to 'Own Business' when adding a new row.
                        
                        businessHtml = `
                            <div class="business-field-container" data-index="${index}">
                                <input type="hidden" 
                                        name="profiles[${index}][business_id]" 
                                        class="business-input-own business-input-${index}" 
                                        value="${currentBusinessId}">
                                <input type="text" 
                                        value="${currentBusinessName}" 
                                        class="business-text-own business-text-${index}" 
                                        disabled style="min-width:150px; background:#f0f0f0; display:block;">
                                        
                                <select name="profiles[${index}][business_id]_disabled" 
                                        class="business-dropdown business-dropdown-${index}" 
                                        style="display:none; min-width:150px;" disabled>
                                    <option value="">Select Business</option>
                                    ${businesses.map(b => `<option value="${b.id}" ${profile.business_id == b.id ? 'selected' : ''}>${b.name}</option>`).join('')}
                                </select>
                            </div>
                        `;
                    }
                }

                row.innerHTML = `
                    <div class="cdbc-profile-selects">
                        <select name="profiles[${index}][user_type_id]" required>
                            <option value="">Select User Type</option>
                            ${userTypes.map(u => `<option value="${u.id}" ${profile.user_type_id == u.id ? 'selected' : ''}>${u.display_name}</option>`).join('')}
                        </select>
                        ${businessHtml}
                    </div>

                    <div class="cdbc-profile-checkbox-row">
                        <div class="cdbc-checkbox profile-checkbox">
                            <input type="checkbox" name="profiles[${index}][default_login]" class="default-login" ${profile.default_login ? 'checked' : ''}>
                            <label>Default Login</label>
                        </div>
                        <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">X Remove</button>
                    </div>
                `;

                document.getElementById('profiles-container').appendChild(row);
                
                // Initialize visibility for the new row if Software Owner
                if (isSoftwareOwnerEmployee) {
                    // নতুন প্রোফাইল তৈরি করার সময়, এটি রেডিও বাটনের নির্বাচিত ভ্যালু অনুসরণ করবে
                    const isOwnSelected = document.getElementById('own_business').checked;
                    updateBusinessFieldVisibility(index, isOwnSelected);
                }


                // Attach event listeners
                attachProfileRowListeners(row, index);
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
                });
            }

            // --- Main Script Execution ---

            // 1. Setup Add Profile Button
            document.getElementById('add-profile-btn').addEventListener('click', function() {
                createProfileRow();
            });

            // 2. Initialize Existing Profiles and attach listeners
            document.querySelectorAll('.cdbc-profile-row').forEach((row, i) => {
                // profileCount কে 0 থেকে শুরু করে সঠিক index ব্যবহার করা হচ্ছে 
                // তবে data-index attribute ব্যবহার করে আরও সঠিক index ব্যবহার করা উচিত
                const index = row.querySelector('.business-field-container')?.dataset.index || i; 
                attachProfileRowListeners(row, index);
            });

            // 3. Ensure only one default login initially
            const checkedBoxes = document.querySelectorAll('.default-login:checked');
            if(checkedBoxes.length > 1){
                checkedBoxes.forEach((cb, i) => { if(i > 0) cb.checked = false; });
            }


            // 4. Tenancy Logic (ONLY for Software Owner Employee)
            if (isSoftwareOwnerEmployee) {
                const ownBusinessRadio = document.getElementById('own_business');
                const anotherBusinessRadio = document.getElementById('another_business');
                const profilesContainer = document.getElementById('profiles-container');
                const ownBusinessHiddenInput = document.getElementById('create_for_own_business');
                
                function handleBusinessAssignmentChange() {
                    const isOwnSelected = ownBusinessRadio.checked;
                    
                    // Update controller signal hidden field
                    ownBusinessHiddenInput.value = isOwnSelected ? '1' : '0';

                    // Update all existing and future profile rows
                    profilesContainer.querySelectorAll('.business-field-container').forEach(container => {
                        const index = container.dataset.index;
                        updateBusinessFieldVisibility(index, isOwnSelected);
                    });
                }
                
                // Attach listeners to radio buttons
                ownBusinessRadio.addEventListener('change', handleBusinessAssignmentChange);
                anotherBusinessRadio.addEventListener('change', handleBusinessAssignmentChange);

                // Initial run to set the correct state on page load/edit
                // রেডিও বাটনের প্রাথমিক ভ্যালু ধরে নিয়ে সঠিক দৃশ্যমানতা সেট করা।
                setTimeout(() => {
                    handleBusinessAssignmentChange();
                }, 10);
            }

        </script>
    @endpush
</x-admin-layout>