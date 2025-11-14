{{-- <table class="cdbc-table">
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
                        @if($profile->userType)
                            <span class="badge badge-warning">{{ $profile->userType->display_name }}</span>
                        @endif
                        @if($profile->business)
                            <span class="badge badge-purple">{{ $profile->business->name }}</span>
                        @endif
                        <br>
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
            <tr><td colspan="7" style="text-align:center;">No users found</td></tr>
        @endforelse
    </tbody>
</table>
 --}}
{{-- <div class="cdbc-pagination">{{ $users->links() }}</div> --}}
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
                        @if($profile->userType)
                            <span class="badge badge-warning">{{ $profile->userType->display_name }}</span>
                        @endif
                        @if($profile->business)
                            <span class="badge badge-purple">{{ $profile->business->name }}</span>
                        @endif
                        @if(!$loop->last)
                            <br>
                        @endif
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
                {{-- <td>
                    <a href="{{ route('admin.users.show', $u->id) }}" class="link-info cdbc-action-btn">Show</a>
                    <a href="{{ route('admin.users.edit', $u->id) }}" class="link-warning cdbc-action-btn">Edit</a>
                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="link-danger cdbc-action-btn" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td> --}}
                <td class="cdbc-action-dropdown-cell">
                    <div class="cdbc-dropdown">
                        {{-- Dropdown Toggle Button (Triple Dot icon, if possible, but using text for simplicity) --}}
                        <button class="cdbc-dropdown-toggle" type="button" aria-expanded="false" >
                            Options <span class="cdbc-caret">▼</span>
                        </button>
                    
                        <div class="cdbc-dropdown-menu">
                            {{-- Show Link --}}
                            <a href="{{ route('admin.users.show', $u->id) }}" class="cdbc-dropdown-item link-info cdbc-action-btn" style="color:black;margin-bottom: 2px;background:#B7BAFD">Show Details</a>
                            
                            {{-- Edit Link --}}
                            <a href="{{ route('admin.users.edit', $u->id) }}" class="cdbc-dropdown-item link-info cdbc-action-btn" style="color:black;margin-bottom: 2px;background:#B7BAFD">Edit User</a>
                            
                            {{-- Delete Form/Button --}}
                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="cdbc-dropdown-item link-info cdbc-action-btn" style="color:black;margin-bottom: 2px;background:#B7BAFD">
                                @csrf @method('DELETE')
                                <button type="submit" class="" style="color:red" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;">No users found</td></tr>
        @endforelse
    </tbody>
</table>
