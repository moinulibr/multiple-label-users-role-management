<x-admin-layout>
    <x-slot name="page_title">
        User List
    </x-slot>

    @push('css')
    <style>
        /* Table and Action Buttons Optimization */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            font-weight: 600;
            color: #6c757d;
            /* Muted header color */
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            vertical-align: middle;
            font-size: 0.95rem;
            /* Slightly smaller font for a cleaner look */
            padding: 12px 15px;
            /* Reduced vertical padding */
        }

        .action-icon {
            padding: 4px 8px;
            /* Smaller, cleaner button size */
            font-size: 0.9rem;
            border-radius: 6px;
            /* Slightly rounded */
            margin-right: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    @endpush

    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0">
            <h4 class="mb-0 text-primary">All Users</h4>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-lg">
                <i class="mdi mdi-plus-circle-outline me-1"></i> ADD NEW USER
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
                                // Dynamic badge color based on role name for better differentiation
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