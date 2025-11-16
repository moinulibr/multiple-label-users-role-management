<x-admin-layout>
    
    {{-- Page Title (header Named Slot) --}}
    <x-slot name="page_title">
        User Profile Management: {{ $user->name }}
    </x-slot>

    {{-- Main Content ($slot) --}}

    <div class="row">
        <div class="col-12">
            
            {{-- Main Card Container with enhanced shadow and rounded corners --}}
            <div class="card card-default shadow-lg rounded-xl">
                <div class="card-header bg-light border-bottom border-primary pt-4 pb-4">
                    <h1 class="card-title font-weight-bold text-primary mb-0">
                        <i class="mdi mdi-account-star-outline mr-2"></i> User: **{{ $user->name }}** <span class="text-muted ml-3 h5">(Phone: {{ $user->phone }})</span>
                    </h1>
                </div>
                <div class="card-body p-5">
                    
                    {{-- Message Display --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="mdi mdi-check-circle-outline mr-2"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="mdi mdi-alert-outline mr-2"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Current Profiles Section --}}
                    <h3 class="mb-4 text-primary border-bottom pb-2 font-weight-bold">
                        <i class="mdi mdi-clipboard-account-outline mr-1"></i> Current Profiles
                    </h3>
                    
                    <div class="row" id="profile-list">
                        @forelse ($userProfiles as $profile)
                            <div class="col-md-4 mb-4">
                                {{-- Profile Card: Visual enhancements --}}
                                @php
                                    $statusClass = $profile->status == 1 ? 'success' : ($profile->status == 0 ? 'warning' : 'danger');
                                @endphp
                                <div class="card h-100 shadow-sm border-left border-{{ $statusClass }} profile-card-item">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="card-title font-weight-bold mb-0 text-{{ $statusClass }}">
                                                <i class="mdi mdi-account-circle mr-1"></i> {{ $profile->userType->display_name ?? 'N/A Type' }}
                                            </h5>
                                            {{-- Default Badge stands out --}}
                                            <span class="badge badge-{{ $profile->default_login ? 'primary' : 'secondary' }} badge-pill p-2 default-badge">
                                                {{ $profile->default_login ? 'DEFAULT' : 'SECONDARY' }}
                                            </span>
                                        </div>
                                        
                                        <ul class="list-unstyled text-sm flex-grow-1">
                                            <li class="mb-1"><small class="text-muted"><i class="mdi mdi-domain mr-1"></i> Business:</small> <br> <strong class="text-dark">{{ $profile->business->name ?? 'None' }}</strong></li>
                                            <li class="mb-1"><small class="text-muted"><i class="mdi mdi-briefcase-account-outline mr-1"></i> Role:</small> <br> 
                                                <strong>
                                                    @if ($profile->roles->count())
                                                        {{ $profile->roles->first()->display_name }}
                                                    @else
                                                        No Role Assigned
                                                    @endif
                                                </strong>
                                            </li>
                                            <li class="mb-1"><small class="text-muted"><i class="mdi mdi-power-settings mr-1"></i> Status:</small> 
                                                <span class="badge badge-{{ $statusClass }} text-uppercase ml-2">
                                                    {{ $profile->status == 1 ? 'Active' : ($profile->status == 0 ? 'Inactive' : 'Suspended') }}
                                                </span>
                                            </li>
                                        </ul>

                                        <div class="mt-3 pt-2 border-top">
                                            {{-- Delete Form (using a button group for clean action placement) --}}
                                            <div class="btn-group w-100" role="group">
                                                <form action="{{ route('user.profiles.destroy', ['user' => $user->id, 'profile' => $profile->id]) }}" method="POST" class="d-inline w-100">
                                                    @csrf
                                                    @method('DELETE')
                                                    {{-- Note: Replaced js confirm() with custom modal logic assumption in a real admin panel --}}
                                                    <button type="submit" onclick="return confirm('Are you sure you want to remove this profile? This cannot be undone.')" class="btn btn-sm btn-outline-danger w-100">
                                                        <i class="mdi mdi-delete mr-1"></i> Remove Profile
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="alert alert-info shadow-sm">
                                    <i class="mdi mdi-information-outline mr-2"></i> No profiles exist for this user. You can add one below.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <hr class="mt-5 mb-5 border-primary">
                    
                    {{-- Add New Profile Section --}}
                    <h3 class="mb-4 text-success font-weight-bold">
                        <i class="mdi mdi-plus-circle-outline mr-1"></i> Add New Profile 
                    </h3>
                    
                    {{-- Form with a distinct background for separation --}}
                    <form action="{{ route('user.profiles.store', $user->id) }}" method="POST">
                        @csrf
                        <div class="row bg-light p-4 rounded-xl border border-success shadow-sm">
                            
                            {{-- Profile Type --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user_type_id" class="font-weight-bold">Profile Type <span class="text-danger">*</span></label>
                                    <select id="user_type_id" name="user_type_id" class="form-control @error('user_type_id') is-invalid @enderror" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach ($userTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('user_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            {{-- Business Assignment --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="business_id" class="font-weight-bold">Business Assignment</label>
                                    <select id="business_id" name="business_id" class="form-control @error('business_id') is-invalid @enderror">
                                        <option value="">-- No Business (e.g., Customer) --</option>
                                        @foreach ($businesses as $business)
                                            <option value="{{ $business->id }}" {{ old('business_id') == $business->id ? 'selected' : '' }}>
                                                {{ $business->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('business_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Role Assignment (Dynamically Loaded) --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="role_id" class="font-weight-bold">Role Assignment</label>
                                    <select id="role_id" name="role_id" class="form-control @error('role_id') is-invalid @enderror">
                                        <option value="">-- Select Business First --</option>
                                    </select>
                                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            {{-- Status and Default Checkbox --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status" class="font-weight-bold">Status</label>
                                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="default_login" name="default_login" value="1" {{ old('default_login') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold" for="default_login">Default Login Profile</label>
                                </div>
                            </div>

                            <div class="col-12 mt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                    <i class="mdi mdi-plus-circle-outline mr-1"></i> Add New Profile
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    
    {{-- Main Content ($slot) --}}
    
    @push('script')
        <script>
            // AJAX Logic for Role Loading
            document.addEventListener('DOMContentLoaded', function () {
                const businessSelect = document.getElementById('business_id');
                const roleSelect = document.getElementById('role_id');

                function loadRoles(businessId) {
                    // Show a loading indicator
                    roleSelect.innerHTML = '<option value="">Loading Roles...</option>';

                    if (businessId) {
                        // **TO-DO: Set your custom AJAX route here to fetch roles**
                        // You need to ensure the backend route is set up to accept the business_id
                        // and return a JSON array of roles (id, display_name).
                        /*
                        fetch('/api/roles-by-business?business_id=' + businessId)
                            .then(response => response.json())
                            .then(roles => {
                                roleSelect.innerHTML = '<option value="">-- No Role --</option>';
                                roles.forEach(role => {
                                    const option = document.createElement('option');
                                    option.value = role.id;
                                    option.textContent = role.display_name;
                                    roleSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error loading roles:', error);
                                roleSelect.innerHTML = '<option value="">Error loading roles</option>';
                            });
                        */
                        // Temporary static options for demo (KEEP THIS FOR NOW):
                        setTimeout(() => {
                            // Check if a role was previously selected (for validation re-fills)
                            const oldRoleId = '{{ old('role_id') }}'; 
                            
                            roleSelect.innerHTML = `<option value="">-- No Role --</option>
                                                    <option value="1" \${oldRoleId == '1' ? 'selected' : ''}>Manager</option>
                                                    <option value="2" \${oldRoleId == '2' ? 'selected' : ''}>Editor</option>`;
                            // Note: The template literal syntax might need adjustment depending on the exact context.
                        }, 500);
                        
                    } else {
                        // If no business is selected
                        roleSelect.innerHTML = '<option value="">-- No Role --</option>';
                    }
                }

                // Event listener to trigger role loading on business change
                businessSelect.addEventListener('change', function() {
                    loadRoles(this.value);
                });

                // Initial load: crucial for cases where the page reloads due to validation errors
                // and 'old' values are present.
                loadRoles(businessSelect.value);
            });
        </script>
    @endpush
    
</x-admin-layout>