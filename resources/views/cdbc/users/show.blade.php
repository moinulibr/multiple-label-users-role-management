<x-admin-layout>
<x-slot name="page_title">Show User</x-slot>

<div class="cdbc-container cdbc-user-show">
    <h2 class="cdbc-title">User Details</h2>

    <div class="cdbc-card">
        <h3>Basic Info</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone }}</p>
        <p><strong>Status:</strong> {{ $user->status==1?'Active':($user->status==0?'Inactive':'Suspended') }}</p>
        <p><strong>Developer Access:</strong> {{ $user->is_developer?'Yes':'No' }}</p>
    </div>

    <div class="cdbc-card" style="margin-top:20px;">
        <h3>User Profiles</h3>
        <ul>
            @foreach($user->profiles as $p)
                <li>
                    {{ $p->userType->display_name ?? 'N/A' }}
                    @if($p->business) - {{ $p->business->name }} @endif
                    @if($p->default_login) (Default Login) @endif
                </li>
            @endforeach
        </ul>
    </div>

    <a href="{{ route('admin.users.index') }}" class="cdbc-btn cdbc-btn-secondary" style="margin-top:20px;">Back</a>
</div>

@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/cdbc-users.css') }}">
@endpush
</x-admin-layout>
