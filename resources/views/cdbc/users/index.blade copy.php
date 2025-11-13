<x-admin-layout>
    <x-slot name="page_title">Users</x-slot>

    <div class="cdbc-container cdbc-user-list">
        <div class="cdbc-header">
            <h2 class="cdbc-title">Users</h2>
            <a href="{{ route('admin.users.create') }}" class="cdbc-btn cdbc-btn-success">+ Create User</a>
        </div>

        {{-- 🔹 Filter Form --}}
        <form id="filter-form" method="GET" action="{{ route('admin.users.index') }}" class="cdbc-filter-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users...">

            <select name="business_id">
                <option value="">All Businesses</option>
                @foreach($businesses as $b)
                <option value="{{ $b->id }}" {{ request('business_id') == $b->id ? 'selected' : '' }}>
                    {{ $b->name }}
                </option>
                @endforeach
            </select>

            <select name="user_type_id">
                <option value="">All User Types</option>
                @foreach($userTypes as $ut)
                <option value="{{ $ut->id }}" {{ request('user_type_id') == $ut->id ? 'selected' : '' }}>
                    {{ $ut->display_name }}
                </option>
                @endforeach
            </select>

            <select name="role_id">
                <option value="">All Roles</option>
                @foreach($roles as $r)
                <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                    {{ $r->display_name ?? $r->name }}
                </option>
                @endforeach
            </select>

            <select name="status">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button type="submit" class="filter-btn">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="reset-btn">Reset</a>
        </form>

        {{-- 🔹 User Table --}}
        <table class="cdbc-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email & Phone</th>
                    <th>Business & User Type</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}<br>{{ $u->phone }}</td>
                    <td>
                        @foreach($u->profiles as $profile)
                        <div class="badge-row">
                            @if($profile->userType)
                            <span class="badge badge-warning">{{ $profile->userType->display_name }}</span>
                            @endif
                            @if($profile->business)
                            <span class="badge badge-purple">{{ $profile->business->name }}</span>
                            @endif
                        </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach($u->profiles as $profile)
                        @foreach($profile->roles as $role)
                        <span class="badge badge-green">{{ $role->display_name ?? $role->name }}</span>
                        @endforeach
                        @endforeach
                    </td>
                    <td>
                        @if($u->status == 1)
                        <span class="badge badge-green">ACTIVE</span>
                        @elseif($u->status == 0)
                        <span class="badge badge-gray">INACTIVE</span>
                        @else
                        <span class="badge badge-red">SUSPENDED</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', $u->id) }}" class="link-info">Show</a>
                        <a href="{{ route('admin.users.edit', $u->id) }}" class="link-warning">Edit</a>
                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="link-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="cdbc-pagination">{{ $users->links() }}</div>
    </div>

    @push('css')
    <style>
        .cdbc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .cdbc-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }

        .cdbc-filter-form input,
        .cdbc-filter-form select {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .filter-btn {
            background: #6c63ff;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
        }

        .reset-btn {
            color: #6c63ff;
            text-decoration: none;
            font-weight: 500;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
            display: inline-block;
            margin: 2px;
        }

        .badge-warning {
            background: #ffd54f;
            color: #000;
        }

        .badge-purple {
            background: #7e57c2;
            color: #fff;
        }

        .badge-green {
            background: #4caf50;
            color: #fff;
        }

        .badge-gray {
            background: #b0bec5;
            color: #000;
        }

        .badge-red {
            background: #e57373;
            color: #fff;
        }

        .link-info,
        .link-warning,
        .link-danger {
            margin-right: 6px;
            font-size: 13px;
            text-decoration: none;
        }

        .link-info {
            color: #5c6bc0;
        }

        .link-warning {
            color: #ffb300;
        }

        .link-danger {
            color: #e53935;
            background: none;
            border: none;
            cursor: pointer;
        }

        .cdbc-btn-success {
            background: #6c63ff;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
    @endpush
</x-admin-layout>