{{-- resources/views/user/_profile_row.blade.php --}}

<div class="cdbc-profile-row" data-index="{{ $index }}">
    {{-- প্রোফাইল ID: আপডেটের সময় এটি ব্যবহার করা যেতে পারে, যদিও বর্তমানে আমরা সব ডিলিট করে ইনসার্ট করছি --}}
    @if(isset($profileData['id']))
        <input type="hidden" name="profiles[{{ $index }}][id]" value="{{ $profileData['id'] }}">
    @endif
    
    <div class="cdbc-profile-selects">
        {{-- User Type --}}
        <select name="profiles[{{ $index }}][user_type_id]" required>
            <option value="">Select User Type</option>
            @foreach ($userTypes as $u)
                <option value="{{ $u->id }}" 
                    {{ old("profiles.{$index}.user_type_id", $profileData['user_type_id'] ?? '') == $u->id ? 'selected' : '' }}>
                    {{ $u->display_name }}
                </option>
            @endforeach
        </select>
        
        {{-- Business Selection (Dynamic based on Tenancy) --}}
        @php
            // যদি এডিট মোড হয়, তবে কোন রেডিও বাটন চেকড, তা থেকে মোড নিতে হবে
            $currentModeOwn = old('create_for_own_business', $is_own_business_creation_mode ?? '1') == '1';
            
            // যদি এটি টেন্যান্ট ইউজার হয়, তবে এটি সব সময় own_business মোড হবে
            if (!$isSoftwareOwnerEmployee) {
                $currentModeOwn = true;
            }
            
            // ইনপুট Field name: মোড অনুযায়ী সক্রিয় বা নিষ্ক্রিয় হবে
            $hiddenInputName = $currentModeOwn ? "profiles[{$index}][business_id]" : "profiles[{$index}][business_id]_disabled";
            $dropdownName = $currentModeOwn ? "profiles[{$index}][business_id]_disabled" : "profiles[{$index}][business_id]";
            
            // এডিটের ক্ষেত্রে, existing value লোড করা হবে
            $selectedBusinessId = old("profiles.{$index}.business_id", $profileData['business_id'] ?? null);
        @endphp

        <div class="business-field-container" data-index="{{ $index }}">
            @if (!$isSoftwareOwnerEmployee)
                {{-- টেন্যান্ট: নিজস্ব ব্যবসা ফিক্সড --}}
                <input type="hidden" name="profiles[{{ $index }}][business_id]" value="{{ $currentBusinessId }}">
                <input type="text" value="{{ $currentBusinessName }}" disabled style="min-width:150px; background:#f0f0f0;">
            @else
                {{-- প্রাইম ইউজার: ডাইনামিক সিলেকশন --}}
                <input type="hidden"
                    name="{{ $hiddenInputName }}"
                    class="business-input-own business-input-{{ $index }}"
                    value="{{ $currentBusinessId }}"
                    {{ $currentModeOwn ? '' : 'disabled' }}>
                
                <input type="text"
                    value="{{ $currentBusinessName }}"
                    class="business-text-own business-text-{{ $index }}"
                    disabled style="min-width:150px; background:#f0f0f0; display:{{ $currentModeOwn ? 'block' : 'none' }};">
                
                <select name="{{ $dropdownName }}"
                    class="business-dropdown business-dropdown-{{ $index }}"
                    style="display:{{ $currentModeOwn ? 'none' : 'block' }}; min-width:150px;"
                    {{ $currentModeOwn ? 'disabled' : '' }}>
                    <option value="">Select Business</option>
                    @foreach ($businesses as $b)
                        <option value="{{ $b->id }}"
                            {{ $selectedBusinessId == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <div class="cdbc-profile-checkbox-row">
        {{-- Default Login Checkbox --}}
        <div class="cdbc-checkbox profile-checkbox">
            <input type="checkbox" id="defaultLogin_{{ $index }}" name="profiles[{{ $index }}][default_login]" class="default-login" value="1" 
                {{ old("profiles.{$index}.default_login", $profileData['default_login'] ?? false) ? 'checked' : '' }}>
            <label for="defaultLogin_{{ $index }}">Default Login</label>
        </div>
        
        {{-- Remove Button --}}
        <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">X Remove</button>
    </div>
</div>