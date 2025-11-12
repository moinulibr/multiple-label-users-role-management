<x-admin-layout>

    <x-slot name="page_title">Role Management</x-slot>

    <div class="rms-container">
        <div class="rms-header d-flex justify-content-between align-items-center">
            <h2 class="rms-title">All Roles</h2>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">+ Add New</a>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Display Name</th>
                            <th>System Name</th>
                            <th>Permissions</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ count(json_decode($role->permissions ?? '[]')) }}</td>
                                <td>
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Delete this role?')" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $roles->links() }}
            </div>
        </div>
    </div>

</x-admin-layout>