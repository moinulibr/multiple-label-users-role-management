<x-admin-layout>
    
    {{-- Page Title (Named Slot: header) --}}
    <x-slot name="page_title">
        Business List
    </x-slot>

    {{-- Custom CSS for List Page --}}
    @push('css')
    <style>
        .cdbc-list-container { max-width: 1400px; margin: 0 auto; }
        .cdbc-list-card { background-color: #ffffff; border-radius: 12px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .cdbc-table thead th { background-color: #f8f9fa; color: #495057; font-weight: 600; border-bottom: 2px solid #e9ecef; }
        .cdbc-table tbody tr:hover { background-color: #f1f1f1; }
        .owner-details { font-size: 0.9rem; color: #6c757d; }
        .status-badge { padding: 0.3em 0.6em; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; }
        .status-active { background-color: #d1e7dd; color: #0f5132; }
        .status-inactive { background-color: #f8d7da; color: #842029; }
        .action-btn { margin-right: 5px; }
    </style>
    @endpush

    {{-- Main Content ($slot) --}}
    
    <div class="cdbc-list-container mt-5 mb-5">
        
        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="cdbc-list-card">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-primary font-weight-bold">Business Management</h4>
                <a href="{{ route('admin.businesses.create') }}" class="btn cdbc-btn-success btn-sm">
                    <i class="mdi mdi-plus-circle-outline"></i> Create New Business
                </a>
            </div>

            <div class="p-4">
                {{-- Search & Filter Form --}}
                <form method="GET" action="{{ route('admin.businesses.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Search by Business/Owner Name, Phone, or Email" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">Filter by Status (All)</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' && request('status') !== null ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-dark">Search</button>
                            <a href="{{ route('admin.businesses.index') }}" class="btn btn-outline-secondary ml-2">Clear</a>
                        </div>
                    </div>
                </form>

                {{-- Business List Table --}}
                <div class="table-responsive">
                    <table class="table table-hover cdbc-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Business Name</th>
                                <th>Owner</th>
                                <th>Contact Info</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($businesses as $business)
                                <tr>
                                    <td>{{ $loop->iteration + $businesses->firstItem() - 1 }}</td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $business->name }}</div>
                                        <div class="owner-details">{{ $business->address }}</div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $business->owner->name ?? 'N/A' }}</div>
                                        <div class="owner-details">Phone: {{ $business->owner->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        Email: {{ $business->email }}<br>
                                        Phone 2: {{ $business->phone2 ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($business->owner && $business->owner->userProfile && $business->owner->userProfile->status == 1)
                                            <span class="status-badge status-active">Active</span>
                                        @else
                                            <span class="status-badge status-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $business->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.businesses.edit', $business->id) }}" class="btn btn-sm btn-info action-btn" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin.businesses.show', $business->id) }}" class="btn btn-sm btn-primary action-btn" title="Show">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        {{-- Delete Form (If applicable) --}}
                                        {{-- <form action="{{ route('admin.businesses.destroy', $business->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this business?')" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-5">
                                        <h4 class="text-muted">No businesses found.</h4>
                                        <p>You can create a new business using the button above.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center mt-4">
                    <div class="cdbc-pagination-container">
                        <div class="pagination-details">
                            Showing {{ $businesses->firstItem() }} to {{ $businesses->lastItem() }} of {{ $businesses->total() }} results
                        </div>
                        <div class="pagination-links">
                            {{ $businesses->links('vendor.pagination.custom') }} 
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
</x-admin-layout>