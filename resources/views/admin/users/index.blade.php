<x-admin-layout>
    <x-slot name="page_title">
        User List
    </x-slot>

    @push('css')
    <style>
        /* Table and Action Buttons Optimization (More refined than before) */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            vertical-align: middle;
            font-size: 0.95rem;
            padding: 10px 15px;
            /* আরও স্লিম করা হলো */
        }

        .action-icon {
            padding: 5px 8px;
            /* বাটন সাইজ ছোট করা হলো */
            font-size: 0.8rem;
            /* আইকন সাইজ ছোট করা হলো */
            border-radius: 4px;
            /* Slightly less rounded */
            margin-right: 2px;
            /* স্পেস কমানো হলো */
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* New User Button (Medium size) */
        .btn-add-user {
            padding: 8px 15px;
            font-size: 0.9rem;
            border-radius: 6px;
        }
    </style>
    @endpush

    {{-- SUCCESS/ERROR MESSAGE HANDLING START --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle-outline me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle-outline me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    {{-- SUCCESS/ERROR MESSAGE HANDLING END --}}

    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0">
            <h4 class="mb-0 text-primary">All Users</h4>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-add-user">
                <i class="mdi mdi-plus me-1"></i> ADD NEW USER
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">NAME</th>
                            <th width="25%">EMAIL</th>
                            <th width="10%">MOBILE</th>
                            <th width="20%">ROLES</th>
                            <th width="20%">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile ?? 'N/A' }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                @php
                                $color = match($role->name) {
                                'super_admin' => 'bg-danger',
                                'admin' => 'bg-info',
                                'manager' => 'bg-success',
                                default => 'bg-secondary',
                                };
                                @endphp
                                <span class="badge {{ $color }} me-1">{{ $role->display_name ?? Str::title(str_replace('_', ' ', $role->name)) }}</span>
                                @empty
                                <span class="badge bg-warning text-dark">No Role</span>
                                @endforelse
                            </td>
                            <td>
                                {{-- View Icon --}}
                                <a href="{{ route('admin.users.show', $user) }}" class="btn action-icon btn-outline-info" title="View">
                                    <i class="mdi mdi-eye"></i>
                                </a>

                                {{-- Edit Icon --}}
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn action-icon btn-outline-warning" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                
                                {{-- Assign Role Icon (NEW ACTION) --}}
                                <a href="{{ route('admin.users.assignRoleForm', $user) }}" class="btn action-icon btn-outline-primary" title="Assign Roles">
                                    <i class="mdi mdi-account-key"></i>
                                </a>

                                {{-- Delete Icon --}}
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn action-icon btn-outline-danger" title="Delete">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="mdi mdi-account-off mdi-48px text-muted"></i>
                                <p class="mt-2 text-muted">No users found. Start by creating a new one.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>