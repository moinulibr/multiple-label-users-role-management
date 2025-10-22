<!-- Card Profile Header -->

<div class="card card-default card-profile">
<div class="card-header-bg" style="background-image:url(https://placehold.co/1200x200/5C6BC0/FFFFFF?text=User+Banner)"></div>

<div class="card-body card-profile-body">
    <div class="profile-avata">
        <img class="rounded-circle" src="{{ auth()->user()->profile_picture
                            ? auth()->user()->profile_picture
                            : asset('assets/admin/images/user/user-xs-01.jpg') }}" alt="Avata Image">
        <a class="h5 d-block mt-3 mb-2" href="#">{{ $user->name }}</a>
        <a class="d-block text-color" href="#">{{ $user->email }}</a>
    </div>

    <ul class="nav nav-profile-follow">
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span class="h5 d-block">1503</span>
                <span class="text-color d-block">Friends</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span class="h5 d-block">2905</span>
                <span class="text-color d-block">Followers</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span class="h5 d-block">1200</span>
                <span class="text-color d-block">Following</span>
            </a>
        </li>
    </ul>

    <div class="profile-button">
        <a class="btn btn-primary btn-pill" href="user-planing-settings.html">Upgrade Plan</a>
    </div>
</div>

<div class="card-footer card-profile-footer">
    <ul class="nav nav-border-top justify-content-center">
        <li class="nav-item">
            <a class="nav-link" href="#">Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Activities</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">Settings</a>
        </li>
    </ul>
</div>

</div>
<!-- End Card Profile Header -->