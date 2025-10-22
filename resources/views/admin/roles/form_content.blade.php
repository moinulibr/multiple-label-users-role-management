{{-- @php
    $rolePermissions = $role->permissions ?? [];
    $formAction = $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store');
@endphp

<h2 style="font-size: 1.5em; margin-bottom: 20px;">
    {{ $role->exists ? 'Edit Role: ' . $role->display_name : 'Create New Role' }}
</h2>

<form action="{{ $formAction }}" method="POST">
    @csrf
    @if ($role->exists)
        @method('PUT')
    @endif
    
    <div style="margin-bottom: 15px;">
        <label for="display_name" style="display: block; font-weight: bold; margin-bottom: 5px;">Role Display Name</label>
        <input type="text" id="display_name" name="display_name" value="{{ old('display_name', $role->display_name) }}" required class="form-control">
    </div>
    
    <div style="margin-bottom: 25px;">
        <label for="name" style="display: block; font-weight: bold; margin-bottom: 5px;">Role Slug (Unique Name)</label>
        <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" class="form-control" placeholder="e.g., content_editor (Leave empty for auto-generation)">
    </div>

    <h3 style="font-size: 1.2em; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 15px;">Assign Permissions</h3>

    <div class="permission-grid">
        @foreach (Config::get('app_permissions.modules') as $module => $actions)
            <div class="module-card">
                <div class="module-title">
                    {{ str_replace('_', ' ', $module) }}
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
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
    
    <div style="margin-top: 30px; text-align: right;">
        <button type="submit" class="btn btn-success">
            {{ $role->exists ? 'Update Role' : 'Create Role' }}
        </button>
    </div>
</form> --}}





<form class="ajaxForm" action="{{ $role->id ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}" method="POST">
    @csrf
    @if($role->id)
        @method('PUT')
    @endif

    <div class="form-group">
        <label>Display Name <span class="required">*</span></label>
        <input type="text" name="display_name" value="{{ old('display_name', $role->display_name) }}" required>
    </div>

    <div class="form-group">
        <label>System Name</label>
        <input type="text" name="name" value="{{ old('name', $role->name) }}">
    </div>

    <div class="form-group">
        <label>Permissions</label>
        <div class="permission-grid">
            @foreach($permissions as $module => $perms)
                <div class="module">
                    <strong>{{ ucfirst($module) }}</strong>
                    @foreach($perms as $perm)
                        <label class="perm">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                {{ in_array($perm, $role->permissions ?? []) ? 'checked' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $perm)) }}
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn primary">Save</button>
        <button type="button" id="closeModal" class="btn">Cancel</button>
    </div>
</form>
