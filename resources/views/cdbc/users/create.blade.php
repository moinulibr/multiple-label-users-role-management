<x-admin-layout>
    <x-slot name="page_title">{{ isset($user) ? 'Edit User' : 'Create User' }}</x-slot>

    <div class="cdbc-container cdbc-user-create">

        <h2 class="cdbc-title">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h2>

        <form action="{{ isset($user) ? route('admin.users.update',$user->id) : route('admin.users.store') }}" method="POST">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div class="cdbc-row">

                <!-- Left Column: User Info -->
                <div class="cdbc-col-left">
                    <div class="cdbc-card">
                        <h3 class="cdbc-card-title">User Information</h3>

                        <label>Name <span class="cdbc-required">*</span></label>
                        <input type="text" name="name" value="{{ old('name',$user->name ?? '') }}" placeholder="Name">

                        <label>Email <span class="cdbc-required">*</span></label>
                        <input type="email" name="email" value="{{ old('email',$user->email ?? '') }}" placeholder="Email Address">

                        <label>Phone <span class="cdbc-required">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone',$user->phone ?? '') }}" placeholder="Primary Phone">

                        <label>Secondary Phone</label>
                        <input type="text" name="secondary_phone" value="{{ old('secondary_phone',$user->secondary_phone ?? '') }}" placeholder="Secondary Phone">
                        
                        <label>Password {{ isset($user)? '(Leave blank to keep current)':'' }}</label>
                        <input type="password" name="password" placeholder="Password">

                        <label>Status</label>
                        <select name="status">
                            <option value="1" {{ (old('status',$user->status ?? '')==1)?'selected':'' }}>Active</option>
                            <option value="0" {{ (old('status',$user->status ?? '')==0)?'selected':'' }}>Inactive</option>
                            <option value="2" {{ (old('status',$user->status ?? '')==2)?'selected':'' }}>Suspended</option>
                        </select>

                        {{-- <div class="cdbc-checkbox">
                            <input type="checkbox" name="is_developer" {{ old('is_developer',$user->is_developer ?? false)?'checked':'' }}>
                            <label>Developer Access</label>
                        </div> --}}

                    </div>
                </div>

                <!-- Right Column: User Profiles -->
                <div class="cdbc-col-right">
                    <div class="cdbc-card">
                        <h3 class="cdbc-card-title">User Profiles</h3>

                        <div id="profiles-container">
                            @php
                                $profiles = old('profiles', isset($user)?$user->profiles->toArray():[[]]);
                            @endphp

                            @foreach($profiles as $p)
                            <div class="cdbc-profile-row">
                                <div class="cdbc-profile-selects">
                                    <select name="profiles[{{$loop->index}}][user_type_id]" required>
                                        <option value="">Select User Type</option>
                                        @foreach($userTypes as $ut)
                                            <option value="{{ $ut->id }}" {{ (isset($p['user_type_id']) && $p['user_type_id']==$ut->id)?'selected':'' }}>{{ $ut->display_name }}</option>
                                        @endforeach
                                    </select>

                                    <select name="profiles[{{$loop->index}}][business_id]">
                                        <option value="">Select Business</option>
                                        @foreach($businesses as $b)
                                            <option value="{{ $b->id }}" {{ (isset($p['business_id']) && $p['business_id']==$b->id)?'selected':'' }}>{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="cdbc-profile-checkbox-row">
                                    <div class="cdbc-checkbox profile-checkbox">
                                        <input type="checkbox" name="profiles[{{$loop->index}}][default_login]" class="default-login" {{ (isset($p['default_login']) && $p['default_login'])?'checked':'' }}>
                                        <label for="profiles[{{$loop->index}}][default_login]">Default Login</label>
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
                <button type="submit" class="cdbc-btn cdbc-btn-success">{{ isset($user) ? 'Update User':'Create User' }}</button>
                <a href="{{ route('admin.users.index') }}" class="cdbc-btn cdbc-btn-secondary">Back</a>
            </div>

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

        /* --- Inputs & Selects --- */
        input[type="text"], input[type="email"], input[type="password"], select {
            width:100%; padding:8px 10px; margin-bottom:12px; border-radius:4px; border:1px solid #ccc;
        }
        label { font-weight:500; margin-bottom:4px; display:block; }

        /* --- Checkbox --- */
        .cdbc-checkbox { display:flex; align-items:center; gap:6px; margin-bottom:12px; }
        .cdbc-checkbox input[type="checkbox"] { width:18px; height:18px; cursor:pointer; }

        /* --- Profile Row --- */
        .cdbc-profile-row { border:1px solid #ddd; border-radius:6px; padding:10px; margin-bottom:10px; background:#fafafa; }
        .cdbc-profile-checkbox-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
        .cdbc-profile-selects { display:flex; gap:10px; flex-wrap:wrap; }
        .cdbc-profile-selects select { min-width:150px; padding:6px 8px; border-radius:4px; border:1px solid #ccc; }

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
    let profileCount = {{ count($profiles) }};
    const userTypes = @json($userTypes);
    const businesses = @json($businesses);

    function createProfileRow(profile={}) {
        profileCount++;
        const row = document.createElement('div');
        row.className = 'cdbc-profile-row';
        row.innerHTML = `
            <div class="cdbc-profile-checkbox-row">
                <div class="cdbc-checkbox profile-checkbox">
                    <input type="checkbox" name="profiles[${profileCount}][default_login]" class="default-login" ${profile.default_login?'checked':''}>
                    <label>Default Login</label>
                </div>
                <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">Remove</button>
            </div>
            <div class="cdbc-profile-selects">
                <select name="profiles[${profileCount}][user_type_id]" required>
                    <option value="">Select User Type</option>
                    ${userTypes.map(u=>`<option value="${u.id}" ${profile.user_type_id==u.id?'selected':''}>${u.display_name}</option>`).join('')}
                </select>
                <select name="profiles[${profileCount}][business_id]">
                    <option value="">Select Business</option>
                    ${businesses.map(b=>`<option value="${b.id}" ${profile.business_id==b.id?'selected':''}>${b.name}</option>`).join('')}
                </select>
            </div>
        `;
        document.getElementById('profiles-container').appendChild(row);

        // --- Default Login Logic ---
        const checkbox = row.querySelector('.default-login');
        checkbox.addEventListener('change', function(){
            if(this.checked){
                document.querySelectorAll('.default-login').forEach(cb=>{
                    if(cb!==this) cb.checked=false;
                });
            }
        });

        // --- Remove Button ---
        const removeBtn = row.querySelector('.remove-profile');
        removeBtn.addEventListener('click', function(){
            row.remove();
        });
    }

    // --- Add Profile Button ---
    document.getElementById('add-profile-btn').addEventListener('click',function(){
        createProfileRow();
    });

    // --- Initialize Existing Profiles ---
    document.querySelectorAll('.cdbc-profile-row').forEach(row=>{
        const checkbox = row.querySelector('.default-login');
        const removeBtn = row.querySelector('.remove-profile');

        checkbox.addEventListener('change', function(){
            if(this.checked){
                document.querySelectorAll('.default-login').forEach(cb=>{
                    if(cb!==this) cb.checked=false;
                });
            }
        });

        removeBtn.addEventListener('click', function(){ row.remove(); });
    });

    // --- Ensure only one default login initially ---
    const checkedBoxes = document.querySelectorAll('.default-login:checked');
    if(checkedBoxes.length>1){
        checkedBoxes.forEach((cb,i)=>{ if(i>0) cb.checked=false; });
    }
</script>
@endpush

</x-admin-layout>
