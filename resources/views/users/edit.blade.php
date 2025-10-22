<h2>ইউজার: {{ $user->name }} কে রোল অ্যাসাইন করুন</h2>

<form action="{{ route('admin.users.assignRoles', $user) }}" method="POST">
    @csrf
    
    <p>বর্তমানে assigned Role: 
        @foreach($user->roles as $role)
            <span style="background-color: #ffc107; padding: 3px 8px; border-radius: 4px;">{{ $role->display_name }}</span>
        @endforeach
    </p>
    
    <label>রোলস নির্বাচন করুন:</label>
    <div>
    @foreach ($roles as $role)
        @php
            // ইউজারটির এই রোলটি আছে কিনা চেক করা
            $isChecked = $user->roles->contains($role->id);
        @endphp
        
        <div style="margin-bottom: 10px;">
            <input 
                type="checkbox" 
                name="roles[]" 
                value="{{ $role->id }}"
                id="role_{{ $role->id }}"
                {{ $isChecked ? 'checked' : '' }}
            >
            <label for="role_{{ $role->id }}">{{ $role->display_name }} ({{ $role->name }})</label>
        </div>
    @endforeach
    </div>

    <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px;">
        Role সেভ করুন
    </button>
</form>