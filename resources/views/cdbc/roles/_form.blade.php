<form action="{{ route('admin.roles.store') }}" method="POST" class="cdbc-form">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="cdbc-form-group">
        <label for="cdbc-role-name">Role Name</label>
        <input type="text" id="cdbc-role-name" name="name"
               class="form-control" value="{{ old('name', $role->name ?? '') }}" required>
    </div>

    <div class="cdbc-permissions-section">
        <h4>Permissions</h4>
        @foreach($modules as $moduleKey => $module)
            @if(in_array($moduleKey, $allowedModules))
                <div class="cdbc-module-block">
                    <h5>{{ $module['label'] }}</h5>
                    <div class="cdbc-actions">
                        @foreach($module['actions'] as $action)
                            @php
                                $key = "$moduleKey.$action";
                            @endphp
                            <label>
                                <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                    {{ in_array($key, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }}>
                                {{ ucfirst($action) }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="cdbc-form-actions">
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Update' : 'Create' }}
        </button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
