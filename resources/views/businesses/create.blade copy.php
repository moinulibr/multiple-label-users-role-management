<x-admin-layout>
    
    {{-- Page Title (Named Slot: header) --}}
    <x-slot name="page_title">
        Create New Business 🚀
    </x-slot>

    {{-- Custom CSS for Elegant Design (using cdbc- prefix) --}}
    @push('css')
    <style>
        /* --- General Reset & Typography --- */
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
        
        /* --- Card & Container Styling --- */
        .cdbc-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .cdbc-card {
            background-color: #ffffff;
            border-radius: 16px; /* Enhanced rounded corners */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04); /* Deeper shadow */
            overflow: hidden;
        }

        .cdbc-header-title {
            color: #1f2937;
            font-size: 1.75rem;
            font-weight: 700;
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .cdbc-body {
            padding: 30px;
        }

        /* --- Form Elements Styling --- */
        .cdbc-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
            display: block;
        }

        .cdbc-input, .cdbc-select, .cdbc-textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease-in-out;
            background-color: #f9fafb;
        }

        .cdbc-input:focus, .cdbc-select:focus, .cdbc-textarea:focus {
            border-color: #06b6d4; /* Teal focus color */
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.3);
            background-color: #ffffff;
        }
        
        .cdbc-input.is-invalid, .cdbc-select.is-invalid, .cdbc-textarea.is-invalid {
            border-color: #ef4444;
        }

        /* --- Dynamic Owner Assignment Section --- */
        .cdbc-owner-assignment {
            background-color: #f0fdf4; /* Very light green */
            border: 2px solid #10b981; /* Primary Green accent */
            border-radius: 12px;
            padding: 20px;
        }
        
        .cdbc-section-title {
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 15px;
        }
        
        /* Transition for field containers */
        .cdbc-field-container {
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            transition: all 0.3s ease-in-out;
            margin-top: 15px;
        }
        
        .cdbc-active-field-container {
            border-color: #06b6d4; /* Teal accent for active state */
            background-color: #ecfeff; /* Lightest teal background */
        }
        
        /* Radio button label styling */
        .cdbc-radio-label {
            cursor: pointer;
            font-weight: 500;
            color: #374151;
            padding: 5px 0;
            display: inline-flex;
            align-items: center;
        }

        /* --- Button Styling --- */
        .cdbc-btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
            font-size: 1rem;
        }

        .cdbc-btn-success {
            background-color: #10b981; /* Primary Green */
            border: none;
            color: #ffffff;
        }

        .cdbc-btn-success:hover {
            background-color: #059669; /* Darker Green on hover */
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.4);
        }
        
        .cdbc-btn-secondary {
            background-color: #6b7280;
            border: none;
            color: #ffffff;
        }

        .cdbc-btn-secondary:hover {
            background-color: #4b5563;
        }

        /* Separator and Layout Enhancements */
        .cdbc-separator {
            border-right: 1px solid #e5e7eb;
        }
        
        @media (max-width: 768px) {
            .cdbc-separator {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }
        }
    </style>
    @endpush

    {{-- Main Content ($slot) --}}
    
    <div class="cdbc-container mt-5 mb-5">
        <div class="row">
            <div class="col-12">
                <div class="cdbc-card">
                    <div class="cdbc-header-title">
                        Business Information & Owner Assignment
                    </div>
                    <div class="cdbc-body">

                        {{-- Message Display --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('businesses.store') }}" method="POST" id="create-business-form">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-7 cdbc-separator">
                                    <h4 class="mb-4 cdbc-section-title text-primary">Business Details</h4>
                                    
                                    <div class="form-group">
                                        <label for="name" class="cdbc-label">Business Name *</label>
                                        <input type="text" name="name" id="name" class="cdbc-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email" class="cdbc-label">Official Email</label>
                                        <input type="email" name="email" id="email" class="cdbc-input @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone2" class="cdbc-label">Secondary Phone</label>
                                                <input type="text" name="phone2" id="phone2" class="cdbc-input @error('phone2') is-invalid @enderror" value="{{ old('phone2') }}">
                                                @error('phone2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="website" class="cdbc-label">Website URL</label>
                                                <input type="url" name="website" id="website" class="cdbc-input @error('website') is-invalid @enderror" value="{{ old('website') }}">
                                                @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="cdbc-label">Full Address</label>
                                        <textarea name="address" id="address" rows="3" class="cdbc-textarea @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-5">
                                    <h4 class="mb-4 cdbc-section-title text-success">Owner & System Settings</h4>
                                    
                                    <div class="cdbc-owner-assignment mb-4">
                                        
                                        {{-- Owner Type Selection --}}
                                        <label class="cdbc-label text-success mb-3">Owner Assignment Method *</label>
                                        <div class="d-flex mb-3">
                                            <div class="custom-control custom-radio mr-4">
                                                <input type="radio" id="owner_existing" name="owner_type" class="custom-control-input" value="existing" checked>
                                                <label class="cdbc-radio-label" for="owner_existing">Assign Existing User</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="owner_new" name="owner_type" class="custom-control-input" value="new">
                                                <label class="cdbc-radio-label" for="owner_new">Create New Owner</label>
                                            </div>
                                        </div>
                                        
                                        {{-- Existing User Fields (Default) --}}
                                        <div id="existing-user-fields" class="cdbc-field-container cdbc-active-field-container">
                                            <p class="font-weight-bold text-info mb-3">Select from registered users</p>
                                            <div class="form-group mb-0">
                                                <label for="user_id" class="cdbc-label">Owner User *</label>
                                                <select name="user_id" id="user_id" class="cdbc-select @error('user_id') is-invalid @enderror">
                                                    <option value="">-- Select an existing user --</option>
                                                    {{-- $users variable comes from the Controller --}}
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} ({{ $user->phone }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                <small class="form-text text-muted mt-2">This user will be granted full ownership access.</small>
                                            </div>
                                        </div>
                                        
                                        {{-- New User Fields (Hidden by default) --}}
                                        <div id="new-user-fields" class="cdbc-field-container">
                                            <p class="font-weight-bold text-danger mb-3">New Owner Account Details</p>
                                            <div class="form-group">
                                                <label for="new_user_name" class="cdbc-label">Owner Name *</label>
                                                <input type="text" name="new_user_name" id="new_user_name" class="cdbc-input @error('new_user_name') is-invalid @enderror" value="{{ old('new_user_name') }}">
                                                @error('new_user_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="new_user_phone" class="cdbc-label">Owner Phone (Login ID) *</label>
                                                <input type="text" name="new_user_phone" id="new_user_phone" class="cdbc-input @error('new_user_phone') is-invalid @enderror" value="{{ old('new_user_phone') }}">
                                                @error('new_user_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="form-group mb-0">
                                                <label for="new_user_password" class="cdbc-label">Password *</label>
                                                <input type="password" name="new_user_password" id="new_user_password" class="cdbc-input @error('new_user_password') is-invalid @enderror">
                                                @error('new_user_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                    </div> {{-- End cdbc-owner-assignment --}}

                                    <div class="form-group">
                                        <label for="parent_business_id" class="cdbc-label">Parent Business (Sub-Tenancy)</label>
                                        <select name="parent_business_id" id="parent_business_id" class="cdbc-select @error('parent_business_id') is-invalid @enderror">
                                            <option value="">-- No Parent Business --</option>
                                            {{-- $parentBusinesses variable comes from the Controller --}}
                                            @foreach ($parentBusinesses as $business)
                                                <option value="{{ $business->id }}" {{ old('parent_business_id') == $business->id ? 'selected' : '' }}>
                                                    {{ $business->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_business_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    <div class="form-group custom-control custom-checkbox pt-2">
                                        <input type="checkbox" class="custom-control-input" id="is_prime" name="is_prime" value="1" {{ old('is_prime') ? 'checked' : '' }}>
                                        <label class="custom-control-label cdbc-radio-label" for="is_prime">Software Ownership (Is Prime Business)</label>
                                    </div>
                                    
                                    <div class="form-group custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="can_manage_roles" name="can_manage_roles" value="1" {{ old('can_manage_roles', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label cdbc-radio-label" for="can_manage_roles">Owner Can Manage Roles</label>
                                    </div>

                                    {{-- Hidden Field for default owner type --}}
                                    <input type="hidden" name="default_owner_type_key" value="rent_owner"> 

                                </div>
                            </div>
                            
                            <div class="mt-5 pt-4 border-top d-flex justify-content-end">
                                <button type="submit" class="btn cdbc-btn cdbc-btn-success btn-lg">
                                    <i class="mdi mdi-check-circle-outline"></i> Create Business & Assign Owner
                                </button>
                                <a href="#" class="btn cdbc-btn cdbc-btn-secondary btn-lg ml-3">Cancel</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Main Content ($slot) --}}

    @push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const existingFields = document.getElementById('existing-user-fields');
            const newFields = document.getElementById('new-user-fields');
            const ownerTypeRadios = document.querySelectorAll('input[name="owner_type"]');
            
            const existingSelect = document.getElementById('user_id');
            const newUserName = document.getElementById('new_user_name');
            const newUserPhone = document.getElementById('new_user_phone');
            const newUserPassword = document.getElementById('new_user_password');

            function toggleOwnerFields(type) {
                if (type === 'existing') {
                    // Show existing, hide new
                    existingFields.style.display = 'block';
                    existingFields.classList.add('cdbc-active-field-container');
                    newFields.style.display = 'none';
                    newFields.classList.remove('cdbc-active-field-container');

                    // Set required for existing, remove for new
                    existingSelect.setAttribute('required', 'required');
                    newUserName.removeAttribute('required');
                    newUserPhone.removeAttribute('required');
                    newUserPassword.removeAttribute('required');
                } else if (type === 'new') {
                    // Show new, hide existing
                    existingFields.style.display = 'none';
                    existingFields.classList.remove('cdbc-active-field-container');
                    newFields.style.display = 'block';
                    newFields.classList.add('cdbc-active-field-container');
                    
                    // Set required for new, remove for existing
                    existingSelect.removeAttribute('required');
                    newUserName.setAttribute('required', 'required');
                    newUserPhone.setAttribute('required', 'required');
                    newUserPassword.setAttribute('required', 'required');
                }
            }

            // Initial setup based on checked radio button (handles old data persistence)
            let initialType = 'existing';
            @if (old('owner_type') === 'new')
                initialType = 'new';
                document.getElementById('owner_new').checked = true;
            @endif
            
            toggleOwnerFields(initialType);
            
            // Add listeners to radio buttons
            ownerTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleOwnerFields(this.value);
                });
            });
        });
    </script>
    @endpush
    
</x-admin-layout>