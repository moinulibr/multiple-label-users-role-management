<x-admin-layout>
    <x-slot name="page_title">
        Assign Roles to: {{ $user->name }}
    </x-slot>

    @push('css')
    <style>
        /* Custom Styles for a clean Role Assignment UI */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .form-check-label {
            cursor: pointer;
            transition: all 0.2s;
            padding: 10px 20px;
            border: 1px solid #ced4da;
            border-radius: 25px; /* Pill shape for roles */
            margin-right: 15px;
            background-color: #f8f9fa;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 10px;
        }
        .form-check-input:checked ~ .form-check-label {
            background-color: #0d6efd; /* Primary color */
            color: white;
            border-color: #0d6efd;
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
        }
        .form-check-input {
            opacity: 0; /* Hide default checkbox */
            position: absolute;
        }
    </style>
    @endpush

    <div class="card shadow">
        <div class="card-header bg-white border-bottom-0">
            <h4 class="mb-0 text-primary">Role Assignment for: <span class="fw-bold">{{ $user->name }}</span></h4>
        </div>
        <div class="card-body">
            
            {{-- ফর্ম অ্যাকশন নতুন assignRoles রাউটের দিকে নির্দেশ করছে --}}
            <form action="{{ route('admin.users.assignRoles', $user) }}" method="POST">
                @csrf

                <div class="alert alert-info border-0">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Select one or more roles to assign to this user. Any previous roles not checked here will be removed.
                </div>
                
                {{-- Roles Assignment (Chekboxes styled as Pills) --}}
                <div class="mb-4 pt-3">
                    <label class="form-label d-block fw-bold text-dark mb-3">Available Roles</label>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($roles as $role)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" 
                                    {{ in_array($role->id, $userRoleIds) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->display_name ?? Str::title(str_replace('_', ' ', $role->name)) }}
                                </label>
                            </div>
                        @empty
                             <p class="text-muted fst-italic">No roles available. Please create roles first.</p>
                        @endforelse
                    </div>
                    @error('roles') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="mdi mdi-check-circle-outline"></i> Update Roles
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-lg">Back to User List</a>
                </div>
            </form>
            
        </div>
    </div>
</x-admin-layout>