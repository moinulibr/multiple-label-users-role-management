@extends('admin.roles.base')
@section('title', 'Assign Roles to User')

@section('content')
    <h2 style="font-size: 1.5em; margin-bottom: 20px;">Assign Roles to: <span style="color: #007bff;">{{ $user->name }}</span></h2>

    <form action="{{ route('admin.users.assignRoles', $user) }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label style="font-size: 1.1em;">Select Roles:</label>
            <div style="padding: 15px; border: 1px solid #ccc; border-radius: 6px; background-color: #fff; margin-top: 10px;">
            @foreach ($roles as $role)
                @php
                    $isChecked = in_array($role->id, $userRoleIds);
                @endphp
                
                <div style="margin-bottom: 8px; display: flex; align-items: center;">
                    <input 
                        type="checkbox" 
                        name="roles[]" 
                        value="{{ $role->id }}"
                        id="role_{{ $role->id }}"
                        {{ $isChecked ? 'checked' : '' }}
                        style="margin-right: 8px;"
                    >
                    <label for="role_{{ $role->id }}" style="font-size: 1em;">
                        {{ $role->display_name }} 
                        <span style="font-size: 0.9em; color: #6c757d;">({{ $role->name }})</span>
                    </label>
                </div>
            @endforeach
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                Save Roles
            </button>
        </div>
    </form>
@endsection