{{-- @extends('admin.roles.base')
@section('title', 'Role & Permission List')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="font-size: 1.5em; margin: 0;">All Roles</h2>
        @can('roles.assign')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                Create New Role
            </a>
        @endcan
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Role Name (Slug)</th>
                    <th>Display Name</th>
                    <th>Total Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr @if($role->name === 'super_admin') style="background-color: #fffacd;" @endif>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->display_name }}</td>
                        <td>{{ count($role->permissions) }}</td>
                        <td>
                            @can('roles.assign')
                                <a href="{{ route('admin.roles.edit', $role) }}" style="color: #007bff; text-decoration: none; margin-right: 10px;">
                                    Edit / Assign Permissions
                                </a>
                            @endcan
                            
                            @if($role->name !== 'super_admin')
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: underline;">
                                        Delete
                                    </button>
                                </form>
                            @else
                                <span style="color: gray;">(Immutable)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection --}}

@extends('admin.roles.base')
@section('title', 'Role & Permission List')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="font-size: 1.5em; margin: 0;">All Roles</h2>
        @can('roles.assign')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary open-modal-link"> 
                Create New Role
            </a>
        @endcan
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Role Name (Slug)</th>
                    <th>Display Name</th>
                    <th>Total Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr @if($role->name === 'super_admin') style="background-color: #fffacd;" @endif>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->display_name }}</td>
                        <td>{{ count($role->permissions) }}</td>
                        <td>
                            @can('roles.assign')
                                <a href="{{ route('admin.roles.edit', $role) }}" class="open-modal-link" style="color: #007bff; text-decoration: none; margin-right: 10px;">
                                    Edit / Assign
                                </a>
                            @endcan
                            
                            @if($role->name !== 'super_admin')
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: underline;">
                                        Delete
                                    </button>
                                </form>
                            @else
                                <span style="color: gray;">(Immutable)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection