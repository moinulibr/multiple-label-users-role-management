@extends('admin.roles.base')
@section('title', $role->exists ? 'Edit Role & Permissions' : 'Create New Role')

@section('content')

    @php
        $rolePermissions = $role->permissions ?? [];
        $formAction = $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store');
    @endphp

    <form action="{{ $formAction }}" method="POST">
        @csrf
        @if ($role->exists)
            @method('PUT')
        @endif
        
        <div class="form-group">
            <label for="display_name">Role Display Name</label>
            <input type="text" id="display_name" name="display_name" value="{{ old('display_name', $role->display_name) }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="name">Role Slug (Unique Name)</label>
            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" class="form-control" placeholder="e.g., content_editor (Leave empty for auto-generation)">
        </div>

        <h3 style="font-size: 1.2em; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 15px; margin-top: 25px;">Assign Permissions</h3>

        <div class="permission-grid">
            @foreach ($permissions as $module => $actions)
                <div class="module-card">
                    <div class="module-title" style="text-transform: capitalize;">
                        {{ str_replace('_', ' ', $module) }}
                    </div>

                    <div style="margin-top: 10px; display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($actions as $action)
                            @php
                                $permissionName = "{$module}.{$action}";
                                $isChecked = in_array($permissionName, $rolePermissions);
                            @endphp
                            
                            <div style="display: flex; align-items: center;">
                                <input 
                                    type="checkbox" 
                                    name="permissions[]" 
                                    value="{{ $permissionName }}"
                                    id="{{ $permissionName }}"
                                    {{ $isChecked ? 'checked' : '' }}
                                    style="margin-right: 8px;"
                                >
                                <label for="{{ $permissionName }}" style="font-size: 0.9em;">
                                    {{ str_replace('_', ' ', $action) }}
                                    <span style="font-size: 0.8em; color: #6c757d;">({{ $permissionName }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-success">
                {{ $role->exists ? 'Update Role' : 'Create Role' }}
            </button>
        </div>
    </form>
@endsection