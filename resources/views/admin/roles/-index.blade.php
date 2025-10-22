@extends('layouts.admin')
@section('title', 'Role & Permission List')

@section('content')
    <div class="card">
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
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="delete-form" style="display: inline-block;">
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
    </div>
@endsection


{{-- 
@extends('layouts.app') 
@section('title', 'Role & Permission List')

@section('content')
    <div style="background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="font-size: 1.5em; margin: 0;">All Roles</h2>
            @can('roles.assign')
                <a href="{{ route('admin.roles.create') }}" class="open-modal-link" style="padding: 10px 18px; background-color: #007bff; color: white; border-radius: 5px; text-decoration: none;"> 
                    Create New Role
                </a>
            @endcan
        </div>
        
    </div>
@endsection --}}


{{-- 

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Role & Permission Management') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">All Roles</h2>
            @can('roles.assign')
                <a href="{{ route('admin.roles.create') }}" class="open-modal-link inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition"> 
                    Create New Role
                </a>
            @endcan
        </div>
        
        </div>

</x-app-layout> --}}