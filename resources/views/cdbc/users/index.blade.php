<x-admin-layout>
<x-slot name="page_title">Users</x-slot>

<div class="cdbc-container cdbc-user-list">
    <div class="cdbc-header">
        <h2 class="cdbc-title">Users</h2>
        <a href="{{ route('admin.users.create') }}" class="cdbc-btn cdbc-btn-primary">+ Create User</a>
    </div>

    {{-- Filter Form --}}
    <form id="filter-form" method="GET" action="{{ route('admin.users.index') }}" class="cdbc-filter-form">
        <input type="text" id="user-search" name="search" value="{{ request('search') }}" placeholder="Search users...">

        <select name="business_id" style="width:18%">
            <option value="">All Businesses</option>
            @foreach($businesses as $b)
                <option value="{{ $b->id }}" {{ request('business_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>

        <select name="user_type_id">
            <option value="">All User Types</option>
            @foreach($userTypes as $ut)
                <option value="{{ $ut->id }}" {{ request('user_type_id') == $ut->id ? 'selected' : '' }}>{{ $ut->display_name }}</option>
            @endforeach
        </select>

        <select name="role_id">
            <option value="">All Roles</option>
            @foreach($roles as $r)
                <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->display_name ?? $r->name }}</option>
            @endforeach
        </select>

        <select name="status">
            <option value="">All Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Suspended</option>
        </select>

        <button type="submit" class="cdbc-btn cdbc-btn-filter">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="cdbc-btn cdbc-btn-reset">Reset</a>
    </form>

    {{-- Table --}}
    <div id="user-table-container">
        @include('cdbc.users.partials.table', ['users' => $users])
    </div>

    <div class="cdbc-pagination-container">
        <div class="pagination-details">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
        </div>
        <div class="pagination-links">
            {{ $users->links('vendor.pagination.custom') }} 
        </div>
    </div>

</div>

@push('css')
<style>
    .cdbc-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .cdbc-filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-bottom: 20px;
    }
    .cdbc-filter-form input,
    .cdbc-filter-form select {
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .cdbc-btn {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
    }
    .cdbc-btn-primary { background: #6c63ff; color: #fff; }
    .cdbc-btn-primary:hover { background: #5a54e0; }
    .cdbc-btn-filter { background: #007bff; color: #fff; border: none; }
    .cdbc-btn-reset { background: #424242; color: #fff; text-decoration: none; }
    .cdbc-btn-reset:hover { background: #d6d6d6; }

    table.cdbc-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border: 1px solid #ddd;
    }
    .cdbc-table th, .cdbc-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }
    .cdbc-table th {
        background: #f8f8f8;
        font-weight: 600;
    }
    .badge {
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        display: inline-block;
        margin: 2px;
    }
    .badge-warning { background: #ffd54f; color: #000; }
    .badge-purple { background: #7e57c2; color: #fff; }
    .badge-green { background: #4caf50; color: #fff; }
    .badge-gray { background: #b0bec5; color: #000; }
    .badge-red { background: #e57373; color: #fff; }
    .link-info, .link-warning, .link-danger {
        margin-right: 6px;
        font-size: 13px;
        text-decoration: none;
    }
    .link-info { color: #5c6bc0; }
    .link-warning { color: #ffb300; }
    .link-danger { color: #e53935; background: none; border: none; cursor: pointer; }
    /* --- Action Links/Buttons --- */
    .cdbc-action-btn { /* নতুন ক্লাস, যা নিচে সংজ্ঞায়িত করা হয়েছে */
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        margin-right: 4px;
        transition: background 0.2s;
        border: 1px solid transparent;
    }
    .link-info.cdbc-action-btn { background: #5c6bc0; color: #fff; border-color: #5c6bc0; } /* Show */
    .link-info.cdbc-action-btn:hover { background: #4a59a7; }
    
    .link-warning.cdbc-action-btn { background: #ffb300; color: #333; border-color: #ffb300; } /* Edit */
    .link-warning.cdbc-action-btn:hover { background: #e6a200; }
    
    /* Delete Button (Keep the same color scheme but apply button style) */
    .link-danger.cdbc-action-btn { 
        background: #e53935; 
        color: #fff; 
        border: none; /* Already defined as button in HTML */
    }
    .link-danger.cdbc-action-btn:hover { background: #c62828; }

    /* --- Pagination Styling --- */
 .cdbc-pagination-container {
        margin-top: 20px;
        display: flex;
        justify-content: space-between; 
        align-items: center;
        padding: 10px 0;
        font-size: 14px;
    }
    .cdbc-pagination-container .pagination-details {
        color: #555;
        font-weight: 500;
    }

    /* কাস্টম টেমপ্লেট (cdbc-pagination) অনুযায়ী CSS আপডেট করা */
    .cdbc-pagination-links {
        display: block; /* Flexbox ভেতরেই থাকবে */
    }
    .cdbc-pagination {
        display: flex; /* UL কে Flex করা */
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .cdbc-pagination .cdbc-page-item {
        margin: 0 2px;
    }
    .cdbc-pagination .cdbc-page-link {
        display: block;
        padding: 6px 10px;
        text-decoration: none;
        color: #6c63ff;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: 0.2s;
        min-width: 32px;
        text-align: center;
        background: #fff;
    }
    .cdbc-pagination .cdbc-page-item:not(.active) .cdbc-page-link:hover {
        background: #f0f0ff;
    }
    .cdbc-pagination .cdbc-page-item.active .cdbc-page-link,
    .cdbc-pagination .cdbc-page-item.active .cdbc-page-link:hover {
        background: #6c63ff;
        color: #fff;
        border-color: #6c63ff;
    }
    .cdbc-pagination .cdbc-page-item.disabled .cdbc-page-link {
        color: #aaa;
        background: #f9f9f9;
        cursor: default;
    }

    /* --- Dropdown Action Styles --- */
    .cdbc-action-dropdown-cell {
        position: relative; /* Dropdown মেনু পজিশন করার জন্য প্রয়োজন */
        width: 100px; /* অ্যাকশন সেলের জন্য জায়গা নিশ্চিত করা */
    }
    .cdbc-dropdown {
        position: relative;
        display: inline-block;
    }
    .cdbc-dropdown-toggle {
        background: #6c63ff;
        color: #fff;
        border: 1px solid #6c63ff;
        padding: 2px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 10px;
        font-weight: 500;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .cdbc-dropdown-toggle:hover {
        background: #5a54e0;
    }

    .cdbc-dropdown-menu {
        position: absolute;
        right: 0; /* ডানদিকে অ্যালাইন করা */
        top: 100%;
        min-width: 140px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        padding: 5px 0;
        
        /* Default state: hidden */
        display: none; 
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    
    /* Show state (added by JS) */
    .cdbc-dropdown.show .cdbc-dropdown-menu {
        display: block; 
        opacity: 1;
        transform: translateY(0);
    }

    .cdbc-dropdown-item, .cdbc-dropdown-item-form {
        display: block;
        padding: 8px 7px;
        text-decoration: none;
        color: #333;
        font-size: 13px;
        white-space: nowrap;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
    }
    /* Link Color Overrides */
    .cdbc-dropdown-item.link-info { color: #5c6bc0; }
    .cdbc-dropdown-item.link-warning { color: #ffb300; }
    .cdbc-dropdown-item.link-danger { color: #e53935; }


    .cdbc-dropdown-item:hover, .cdbc-dropdown-item-form:hover {
        background: #f5f5f5;
    }
    /* Delete button inside form must match the link style */
    .cdbc-dropdown-item-form button.cdbc-dropdown-item {
        cursor: pointer;
        padding: 8px 15px;
    }
</style>
@endpush

@push('script')
<script>
document.getElementById('user-search').addEventListener('input', function(){
    let val = this.value;

    // Ajax request
    fetch("{{ route('admin.users.index') }}?search=" + encodeURIComponent(val), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        let parser = new DOMParser();
        let doc = parser.parseFromString(html, 'text/html');
        let newTable = doc.querySelector('#user-table-container').innerHTML;
        document.querySelector('#user-table-container').innerHTML = newTable;
    });
});


    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.cdbc-dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const dropdown = this.closest('.cdbc-dropdown');
                
                // Close all other open dropdowns
                document.querySelectorAll('.cdbc-dropdown.show').forEach(openDropdown => {
                    if (openDropdown !== dropdown) {
                        openDropdown.classList.remove('show');
                    }
                });

                // Toggle the current dropdown
                dropdown.classList.toggle('show');
            });
        });

        // Close dropdown if the user clicks outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.cdbc-dropdown')) {
                document.querySelectorAll('.cdbc-dropdown.show').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
    });
</script>
@endpush
</x-admin-layout>
