<x-admin-layout>
<x-slot name="page_title">Users</x-slot>

<div class="cdbc-container cdbc-user-list">
    <h2 class="cdbc-title">Users</h2>

    <div class="cdbc-search">
        <input type="text" id="user-search" placeholder="Search users...">
        <a href="{{ route('admin.users.create') }}" class="cdbc-btn cdbc-btn-primary">+ Create User</a>
    </div>

    <table class="cdbc-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email & Phone</th>
                <th>Profiles & Business</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="user-table-body">
            @foreach($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td>
                  {{ $u->email }}
                  <br/>
                  {{ $u->phone }}
                </td>
                <td>{{ $u->profiles->count() }}</td>
                <td></td>
                <td>
                    @if($u->status==1) Active @elseif($u->status==0) Inactive @else Suspended @endif
                </td>
                <td>
                    <a href="{{ route('admin.users.show',$u->id) }}" class="cdbc-btn cdbc-btn-info">Show</a>
                    <a href="{{ route('admin.users.edit',$u->id) }}" class="cdbc-btn cdbc-btn-warning">Edit</a>
                    <form action="{{ route('admin.users.destroy',$u->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="cdbc-btn cdbc-btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="cdbc-pagination">
        {{ $users->links() }}
    </div>
</div>

@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/cdbc-users.css') }}">
@endpush

@push('script')
<script>
    // simple live search
    document.getElementById('user-search').addEventListener('input', function(){
        let val = this.value.toLowerCase();
        document.querySelectorAll('#user-table-body tr').forEach(tr=>{
            let text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(val)?'':'none';
        });
    });
</script>
@endpush
</x-admin-layout>
