<x-admin-layout>
    <x-slot name="page_title">
        User Details: {{ $user->name }}
    </x-slot>

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">User Information</h4>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm text-dark">
                <i class="mdi mdi-pencil"></i> Edit User
            </a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Name:</dt>
                <dd class="col-sm-9">{{ $user->name }}</dd>
                
                <dt class="col-sm-3">Email:</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>
                
                <dt class="col-sm-3">Mobile:</dt>
                <dd class="col-sm-9">{{ $user->mobile ?? 'N/A' }}</dd>
                
                <dt class="col-sm-3">Account Created:</dt>
                <dd class="col-sm-9">{{ $user->created_at->format('M d, Y H:i A') }}</dd>
                
                <dt class="col-sm-3">Last Updated:</dt>
                <dd class="col-sm-9">{{ $user->updated_at->format('M d, Y H:i A') }}</dd>
                
                <hr class="my-3">
                
                <dt class="col-sm-3">Assigned Roles:</dt>
                <dd class="col-sm-9">
                    @forelse ($user->roles as $role)
                        <span class="badge bg-primary text-white me-1 p-2">{{ $role->display_name ?? $role->name }}</span>
                    @empty
                        <span class="badge bg-secondary p-2">No Role Assigned</span>
                    @endforelse
                </dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</x-admin-layout>