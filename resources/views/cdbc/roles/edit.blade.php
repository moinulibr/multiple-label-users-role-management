<x-admin-layout>
    <x-slot name="page_title">Edit Role</x-slot>

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header"><h4>Edit Role</h4></div>
            <div class="card-body">

                <div class="form-group mb-3">
                    <label>Display Name</label>
                    <input type="text" name="display_name" class="form-control" value="{{ $role->display_name }}" required>
                </div>

                <div class="form-group mb-3">
                    <label>System Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                </div>

                <div class="form-group mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $role->description }}</textarea>
                </div>

                <div class="form-group mb-3">
                    <label><strong>Permissions</strong></label>
                    <div class="row">
                        @foreach ($permissions as $group => $perms)
                            <div class="col-md-3 mb-3">
                                <div class="border p-2 rounded">
                                    <h6 class="fw-bold text-primary">{{ ucfirst($group) }}</h6>
                                    @foreach ($perms as $key => $label)
                                        @php
                                            $permValue = $group . '.' . $key;
                                        @endphp
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permValue }}" 
                                                class="form-check-input"
                                                {{ in_array($permValue, $selectedPermissions ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- <div class="form-check mb-3">
                    <input type="checkbox" name="is_special" class="form-check-input" value="1" {{ $role->is_special ? 'checked' : '' }}>
                    <label class="form-check-label">Special Role (System / Global)</label>
                </div> --}}

            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</x-admin-layout>
