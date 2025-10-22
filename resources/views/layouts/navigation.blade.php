
<!-- search form -->
<div class="search-form">
    <!-- Search form can be left static or you can define a search route -->
    <!-- যদি Laravel route ব্যবহার করতে চান: <form action="" method="get"> -->
    <form action="#" method="get"> 
        <div class="input-group input-group-sm" id="input-group-search">
            <input type="text" autocomplete="off" name="query" id="search-input" class="form-control" placeholder="Search..." />
            <div class="input-group-append">
                <button class="btn" type="button">/</button>
            </div>
        </div>
    </form>
    <!-- Search Dropdown - keeping content static as the links are non-functional -->
    <ul class="dropdown-menu dropdown-menu-search">
        <li class="nav-item"><a class="nav-link" href="#">Morbi leo risus</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Dapibus ac facilisis in</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Porta ac consectetur ac</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Vestibulum at eros</a></li>
    </ul>
</div>

<ul class="nav navbar-nav">
    <!-- Offcanvas Toggler for Contacts/Sidebar -->
    <li class="custom-dropdown">
        <!-- এই টগলারের জন্য custom JS (যেমন theme.js) লোড হওয়া আবশ্যক -->
        <a class="offcanvas-toggler active custom-dropdown-toggler" data-offcanvas="contact-off" href="javascript:">
            <i class="mdi mdi-contacts icon"></i>
        </a>
    </li>
    
    <!-- Notification Dropdown -->
    <li class="custom-dropdown">
        <!-- Bootstrap JS লোড হওয়া আবশ্যক -->
        <button class="notify-toggler custom-dropdown-toggler">
            <i class="mdi mdi-bell-outline icon"></i>
            <!-- Badge for dynamic notification count -->
            <span class="badge badge-xs rounded-circle">{{-- Notification Count Variable Here --}}</span> 
        </button>
        <div class="dropdown-notify">

            <header>
                <!-- Bootstrap Tab JS লোড হওয়া আবশ্যক -->
                <div class="nav nav-underline" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="all-tabs" data-toggle="tab" href="#all" role="tab" aria-controls="nav-home" aria-selected="true">All (5)</a>
                    <a class="nav-item nav-link" id="message-tab" data-toggle="tab" href="#message" role="tab" aria-controls="nav-profile" aria-selected="false">Msgs (4)</a>
                    <a class="nav-item nav-link" id="other-tab" data-toggle="tab" href="#other" role="tab" aria-controls="nav-contact" aria-selected="false">Others (3)</a>
                </div>
            </header>

            <!-- SimpleBar JS লোড হওয়া আবশ্যক, না হলে scrolling কাজ করবে না -->
            <div class="" data-simplebar style="height: 325px;">
                <div class="tab-content" id="myTabContent">
                    <!-- All Tab Content - Static for this example -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tabs">
                        <!-- Image Path Fix: asset() is correctly used -->
                        <div class="media media-sm bg-warning-10 p-4 mb-0">
                            <div class="media-sm-wrapper">
                                <a href="#">
                                    <img src="{{ asset('assets/admin/images/user/user-sm-02.jpg') }}" alt="User Image">
                                </a>
                            </div>
                            <div class="media-body">
                                <a href="#">
                                    <span class="title mb-0">John Doe</span>
                                    <span class="discribe">Extremity sweetness difficult behaviour he of. On disposal of as landlord horrible.</span>
                                    <span class="time"><time>Just now</time>...</span>
                                </a>
                            </div>
                        </div>
                        <!-- ... other notifications with image paths fixed ... -->
                        <div class="media media-sm p-4 bg-light mb-0">
                            <div class="media-sm-wrapper bg-primary"><a href="#"><i class="mdi mdi-calendar-check-outline"></i></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">New event added</span><span class="discribe">1/3/2014 (1pm - 2pm)</span><span class="time"><time>10 min ago...</time>...</span></a></div>
                        </div>
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper"><a href="#"><img src="{{ asset('assets/admin/images/user/user-sm-03.jpg') }}" alt="User Image"></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Sagge Hudson</span><span class="discribe">On disposal of as landlord Afraid at highly months do things on at.</span><span class="time"><time>1 hrs ago</time>...</span></a></div>
                        </div>
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper bg-info-dark"><a href="#"><i class="mdi mdi-account-multiple-check"></i></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Add request</span><span class="discribe">Add Dany Jones as your contact.</span><div class="buttons"><a href="#" class="btn btn-sm btn-success shadow-none text-white">accept</a><a href="#" class="btn btn-sm shadow-none">delete</a></div><span class="time"><time>6 hrs ago</time>...</span></a></div>
                        </div>
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper bg-info"><a href="#"><i class="mdi mdi-playlist-check"></i></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Task complete</span><span class="discribe">Afraid at highly months do things on at.</span><span class="time"><time>1 hrs ago</time>...</span></a></div>
                        </div>
                    </div>

                    <!-- Message Tab Content - Static for this example -->
                    <div class="tab-pane fade" id="message" role="tabpanel" aria-labelledby="message-tab">
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper"><a href="#"><img src="{{ asset('assets/admin/images/user/user-sm-01.jpg') }}" alt="User Image"></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Selena Wagner</span><span class="discribe">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</span><span class="time"><time>15 min ago</time>...</span></a></div>
                        </div>
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper"><a href="#"><img src="{{ asset('assets/admin/images/user/user-sm-03.jpg') }}" alt="User Image"></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Sagge Hudson</span><span class="discribe">On disposal of as landlord Afraid at highly months do things on at.</span><span class="time"><time>1 hrs ago</time>...</span></a></div>
                        </div>
                        <div class="media media-sm bg-warning-10 p-4 mb-0">
                            <div class="media-sm-wrapper"><a href="#"><img src="{{ asset('assets/admin/images/user/user-sm-02.jpg') }}" alt="User Image"></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">John Doe</span><span class="discribe">Extremity sweetness difficult behaviour he of. On disposal of as landlord horrible. Afraid at highly months do things on at.</span><span class="time"><time>Just now</time>...</span></a></div>
                        </div>
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper"><a href="#"><img src="{{ asset('assets/admin/images/user/user-sm-04.jpg') }}" alt="User Image"></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Albrecht Straub</span><span class="discribe"> Beatae quia natus assumenda laboriosam, nisi perferendis aliquid consectetur expedita non tenetur.</span><span class="time"><time>Just now</time>...</span></a></div>
                        </div>
                    </div>

                    <!-- Other Tab Content - Static for this example -->
                    <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="contact-tab">
                        <div class="media media-sm p-4 bg-light mb-0">
                            <div class="media-sm-wrapper bg-primary"><a href="#"><i class="mdi mdi-calendar-check-outline"></i></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">New event added</span><span class="discribe">1/3/2014 (1pm - 2pm)</span><span class="time"><time>10 min ago...</time>...</span></a></div>
                        </div>
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper bg-info-dark"><a href="#"><i class="mdi mdi-account-multiple-check"></i></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Add request</span><span class="discribe">Add Dany Jones as your contact.</span><div class="buttons"><a href="#" class="btn btn-sm btn-success shadow-none text-white">accept</a><a href="#" class="btn btn-sm shadow-none">delete</a></div><span class="time"><time>6 hrs ago</time>...</span></a></div>
                        </div>
                        <div class="media media-sm p-4 mb-0">
                            <div class="media-sm-wrapper bg-info"><a href="#"><i class="mdi mdi-playlist-check"></i></a></div>
                            <div class="media-body"><a href="#"><span class="title mb-0">Task complete</span><span class="discribe">Afraid at highly months do things on at.</span><span class="time"><time>1 hrs ago</time>...</span></a></div>
                        </div>
                    </div>

                </div>
            </div>

            <footer class="border-top dropdown-notify-footer">
                <div class="d-flex justify-content-between align-items-center py-2 px-4">
                    <span>Last updated 3 min ago</span>
                    <a id="refress-button" href="javascript:" class="btn mdi mdi-cached btn-refress"></a>
                </div>
            </footer>

        </div>
    </li>
    
    <!-- User Account Dropdown -->
    @auth
    <li class="dropdown user-menu">
        <!-- Bootstrap JS লোড হওয়া আবশ্যক -->
        <button class="dropdown-toggle nav-link" data-toggle="dropdown">
            <img src="{{ auth()->user()->profile_picture
                            ? auth()->user()->profile_picture
                            : asset('assets/admin/images/user/user-xs-01.jpg') }}" class="user-image rounded-circle" alt="User Image" />
            <span class="d-none d-lg-inline-block">{{ Auth::user()->name }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li>
                <!-- Assuming you have a 'profile.edit' route for user profile -->
                <a class="dropdown-link-item" href="{{ route('profile.edit') }}">
                    <i class="mdi mdi-account-outline"></i>
                    <span class="nav-text">My Profile</span>
                </a>
            </li>
            <li><a class="dropdown-link-item" href="#"><i class="mdi mdi-email-outline"></i><span class="nav-text">Message</span><span class="badge badge-pill badge-primary">24</span></a></li>
            <li><a class="dropdown-link-item" href="#"><i class="mdi mdi-diamond-stone"></i><span class="nav-text">Activities</span></a></li>
            <li><a class="dropdown-link-item" href="#"><i class="mdi mdi-settings"></i><span class="nav-text">Account Setting</span></a></li>

            <li class="dropdown-footer">
                <!-- Logout Implementation for Laravel -->
                <form method="POST" action="{{ route('logout') }}" id="logout-form-desktop" style="display: none;">
                    @csrf
                </form>
                <a class="dropdown-link-item" href="#" 
                    onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();"> 
                    <i class="mdi mdi-logout"></i> Log Out 
                </a>
            </li>
        </ul>
    </li>
    @endauth
</ul>

