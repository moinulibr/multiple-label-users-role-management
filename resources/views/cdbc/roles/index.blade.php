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


{{-- <x-admin-layout>
<x-slot name="page_title">Roles</x-slot>


<div class="cdbc-container">
<div class="cdbc-grid">
<div class="cdbc-card">
<div class="cdbc-card-header">
<h3 class="cdbc-title">Roles</h3>
<a href="{{ route('admin.roles.create') }}" class="cdbc-btn cdbc-btn-primary">Create Role</a>
</div>


<div class="cdbc-card-body">
<table class="cdbc-table">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Display</th>
<th>Business</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
@foreach($roles as $r)
<tr>
<td>{{ $r->id }}</td>
<td>{{ $r->name }}</td>
<td>{{ $r->display_name }}</td>
<td>{{ optional($r->business)->name ?? '—' }}</td>
<td>
<a href="{{ route('admin.roles.edit',$r) }}" class="cdbc-link">Edit</a>
<form action="{{ route('admin.roles.destroy',$r) }}" method="POST" style="display:inline">
@csrf @method('DELETE')
<button class="cdbc-link cdbc-link-danger" onclick="return confirm('Delete?')">Delete</button>
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
</div>


@push('css')
<link rel="stylesheet" href="{{ asset('css/cdbc-admin.css') }}">
@endpush
</x-admin-layout> --}}