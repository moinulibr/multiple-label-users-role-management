<x-admin-layout>
    <x-slot name="page_title">Create User</x-slot>

    <div class="cdbc-container">
        <h2 class="cdbc-page-title">Create New User</h2>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="cdbc-flex-row">
                <!-- Left side: User Information -->
                <div class="cdbc-card">
                    <h3 class="cdbc-card-title">User Information</h3>

                    <div class="cdbc-form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Full name" required>
                    </div>

                    <div class="cdbc-form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email address">
                    </div>

                    <div class="cdbc-form-group">
                        <label for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="text" id="phone" name="phone" placeholder="Primary phone" required>
                    </div>

                    <div class="cdbc-form-group">
                        <label for="secondary_phone">Secondary Phone</label>
                        <input type="text" id="secondary_phone" name="secondary_phone" placeholder="Secondary phone">
                    </div>

                    <div class="cdbc-form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password">
                    </div>
                </div>

                <!-- Right side: User Profiles -->
                <div class="cdbc-card">
                    <h3 class="cdbc-card-title">User Profiles</h3>

                    <div id="profiles-container">
                        <!-- Default Profile Row -->
                        @php $index = 0; @endphp
                        <div class="cdbc-profile-row" data-index="{{ $index }}">
                            <div class="cdbc-profile-main">
                                <div class="cdbc-profile-selects">
                                    <div class="cdbc-input-group">
                                        <label>User Type <span class="text-danger">*</span></label>
                                        <select name="profiles[{{ $index }}][user_type_id]" required>
                                            <option value="">Select User Type</option>
                                            @foreach($userTypes as $ut)
                                                <option value="{{ $ut->id }}">{{ $ut->display_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="cdbc-input-group">
                                        <label>Business (optional)</label>
                                        <select name="profiles[{{ $index }}][business_id]">
                                            <option value="">Select Business</option>
                                            @foreach($businesses as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="cdbc-profile-actions">
                                    <label class="cdbc-checkbox-label">
                                        <input type="checkbox" name="profiles[{{ $index }}][default_login]" class="default-login">
                                        Default Login
                                    </label>

                                    <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-profile" class="cdbc-btn cdbc-btn-primary mt-2">+ Add Profile</button>
                </div>
            </div>

            <div class="cdbc-submit">
                <button type="submit" class="cdbc-btn cdbc-btn-success">Save User</button>
            </div>
        </form>
    </div>

    {{-- ================== CSS ================== --}}
    <style>
        .cdbc-container { padding: 20px; }
        .cdbc-page-title { font-size: 22px; font-weight: 700; margin-bottom: 20px; }
        .cdbc-flex-row { display: flex; gap: 20px; flex-wrap: wrap; }
        .cdbc-card {
            background: #fff;
            flex: 1;
            border: 1px solid #e3e3e3;
            border-radius: 10px;
            padding: 20px;
            min-width: 320px;
        }
        .cdbc-card-title { font-size: 16px; font-weight: 700; margin-bottom: 15px; }

        .cdbc-form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        .cdbc-form-group label { font-weight: 600; margin-bottom: 6px; }
        .cdbc-form-group input {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 14px;
            background-color: #fafafa;
        }

        .cdbc-profile-row {
            background: #fff;
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            transition: 0.2s;
        }
        .cdbc-profile-row:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.05); }

        .cdbc-profile-selects {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }
        .cdbc-input-group { flex: 1; display: flex; flex-direction: column; }
        .cdbc-input-group label { font-weight: 600; font-size: 14px; margin-bottom: 5px; color: #444; }
        .cdbc-input-group select {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 14px;
            background-color: #fafafa;
        }

        .cdbc-profile-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cdbc-checkbox-label {
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #333;
        }
        .cdbc-btn {
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.2s;
        }
        .cdbc-btn-primary { background-color: #007bff; color: #fff; }
        .cdbc-btn-primary:hover { background-color: #0069d9; }
        .cdbc-btn-danger { background-color: #f44336; color: #fff; }
        .cdbc-btn-danger:hover { background-color: #d32f2f; }
        .cdbc-btn-success { background-color: #28a745; color: #fff; }
        .cdbc-btn-success:hover { background-color: #218838; }

        .cdbc-submit { margin-top: 20px; text-align: right; }
    </style>

    {{-- ================== JS ================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let profileIndex = 1;

            // ✅ Add new profile
            document.getElementById('add-profile').addEventListener('click', function () {
                const container = document.getElementById('profiles-container');
                const newProfile = document.createElement('div');
                newProfile.classList.add('cdbc-profile-row');
                newProfile.setAttribute('data-index', profileIndex);

                newProfile.innerHTML = `
                    <div class="cdbc-profile-main">
                        <div class="cdbc-profile-selects">
                            <div class="cdbc-input-group">
                                <label>User Type <span class="text-danger">*</span></label>
                                <select name="profiles[${profileIndex}][user_type_id]" required>
                                    <option value="">Select User Type</option>
                                    @foreach($userTypes as $ut)
                                        <option value="{{ $ut->id }}">{{ $ut->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="cdbc-input-group">
                                <label>Business (optional)</label>
                                <select name="profiles[${profileIndex}][business_id]">
                                    <option value="">Select Business</option>
                                    @foreach($businesses as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="cdbc-profile-actions">
                            <label class="cdbc-checkbox-label">
                                <input type="checkbox" name="profiles[${profileIndex}][default_login]" class="default-login">
                                Default Login
                            </label>
                            <button type="button" class="cdbc-btn cdbc-btn-danger remove-profile">Remove</button>
                        </div>
                    </div>
                `;
                container.appendChild(newProfile);
                profileIndex++;
            });

            // ✅ Only one default login
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('default-login')) {
                    if (e.target.checked) {
                        document.querySelectorAll('.default-login').forEach(chk => {
                            if (chk !== e.target) chk.checked = false;
                        });
                    }
                }
            });

            // ✅ Remove profile
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-profile')) {
                    e.target.closest('.cdbc-profile-row').remove();
                }
            });
        });
    </script>
</x-admin-layout>
