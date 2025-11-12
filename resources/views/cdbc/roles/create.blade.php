<x-admin-layout>
    <x-slot name="page_title">Create Role</x-slot>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-header"><h4>Create New Role</h4></div>
            <div class="card-body">

                <div class="form-group mb-3">
                    <label>Display Name</label>
                    <input type="text" name="display_name" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label>System Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="form-group mb-3">
                    <label><strong>Permissions</strong></label>
                    <div class="row">
                        @foreach ($permissions as $group => $perms)
    <div class="col-md-3 mb-3">
        <div class="border p-2 rounded">
            <h6 class="fw-bold text-primary">{{ ucfirst($group) }}</h6>

            @foreach ($perms as $key => $label)
                @if (is_array($label))
                    {{-- Nested group --}}
                    <strong class="text-secondary d-block mt-1">{{ ucfirst($key) }}</strong>
                    @foreach ($label as $subKey => $subLabel)
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="permissions[]" 
                                   value="{{ $group . '.' . $key . '.' . $subKey }}" 
                                   class="form-check-input"
                                   {{ isset($selectedPermissions) && in_array($group . '.' . $key . '.' . $subKey, $selectedPermissions ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $subLabel }}</label>
                        </div>
                    @endforeach
                @else
                    {{-- Normal permission --}}
                    <div class="form-check">
                        <input type="checkbox" 
                               name="permissions[]" 
                               value="{{ $group . '.' . $key }}" 
                               class="form-check-input"
                               {{ isset($selectedPermissions) && in_array($group . '.' . $key, $selectedPermissions ?? []) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $label }}</label>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endforeach

                    </div>
                </div>

                {{-- <div class="form-check mb-3">
                    <input type="checkbox" name="is_special" class="form-check-input" value="1">
                    <label class="form-check-label">Special Role (System / Global)</label>
                </div> --}}

            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</x-admin-layout>
