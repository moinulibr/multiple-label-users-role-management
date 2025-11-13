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

        <select name="business_id">
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
    .cdbc-btn-reset { background: #e0e0e0; color: #333; text-decoration: none; }
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
</script>
@endpush
</x-admin-layout>
