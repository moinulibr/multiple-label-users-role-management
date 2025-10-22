<x-admin-layout>
    <x-slot name="page_title">
        {{ $user->exists ? 'Edit User: ' . $user->name : 'Create New User' }}
    </x-slot>

    @push('css')
    <style>
        /* Custom Styles for a modern look */
        .form-control {
            border-radius: 8px; /* Slightly rounded corners */
            padding: 10px 15px; /* Comfortable padding */
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); /* Lighter shadow */
        }
        .form-check-label {
            cursor: pointer;
            transition: all 0.2s;
            padding: 8px 15px;
            border: 1px solid #ced4da;
            border-radius: 20px; /* Pill shape for roles */
            margin-right: 10px;
            background-color: #f8f9fa;
            font-weight: 500;
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
        /* Custom spacing for field pairs like Password/Confirm */
        .input-pair > .col-md-6:first-child {
            padding-right: 15px;
        }
        .input-pair > .col-md-6:last-child {
            padding-left: 15px;
        }
    </style>
    @endpush

    <div class="card shadow">
        <div class="card-header bg-white border-bottom-0">
            <h4 class="mb-0 text-primary">{{ $user->exists ? 'Edit User Details' : 'Add New User' }}</h4>
        </div>
        <div class="card-body pt-0">
            
            <form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
                @csrf
                @if ($user->exists)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}">
                        @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3 input-pair">
                        <label class="form-label d-block text-muted">Roles Assignment</label>
                        <hr class="my-0"> 
                    </div>
                </div>


                {{-- Password Fields (Row-এর ভেতরে ডাবল কলাম করে ডিজাইন আরও ক্লিন করা হলো) --}}
                <div class="row input-pair">
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label">Password @if(!$user->exists) <span class="text-danger">*</span> @else <small class="text-muted">(Leave blank to keep current)</small> @endif</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" @if(!$user->exists) required @endif>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password @if(!$user->exists) <span class="text-danger">*</span> @endif</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" @if(!$user->exists) required @endif>
                    </div>
                </div>

                <hr class="my-4">
                
                {{-- Roles Assignment (Chekboxes styled as Pills) --}}
                <div class="mb-4">
                    <label class="form-label d-block fw-bold text-dark">Assign Roles</label>
                    <div class="d-flex flex-wrap gap-2 mt-2">
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

                <div class="d-flex justify-content-end pt-3">
                    <button type="submit" class="btn btn-primary btn-md me-3">
                        <i class="mdi {{ $user->exists ? 'mdi-content-save' : 'mdi-plus-circle' }}"></i> 
                        {{ $user->exists ? 'Update User' : 'Create User' }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-md">Cancel</a>
                </div>
            </form>
            
        </div>
    </div>
</x-admin-layout>