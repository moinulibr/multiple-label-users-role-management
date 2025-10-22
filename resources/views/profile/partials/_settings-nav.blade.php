<!-- Left Side Settings Navigation (col-xl-3) -->

<div class="card card-default">
<div class="card-header">
<h2>Settings</h2>
</div>
<div class="card-body pt-0">
<ul class="nav nav-settings">
<li class="nav-item">
{{-- 'profile' active আছে কিনা চেক করা হচ্ছে --}}
<a class="nav-link @if(isset($active_setting) && $active_setting == 'profile') active @endif" href="#profile">
<i class="mdi mdi-account-outline mr-1"></i> Profile
</a>
</li>
<li class="nav-item">
{{-- 'account' active আছে কিনা চেক করা হচ্ছে --}}
<a class="nav-link @if(isset($active_setting) && $active_setting == 'account') active @endif" href="#account">
<i class="mdi mdi-settings-outline mr-1"></i> Account
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#password">
<i class="mdi mdi-lock-outline mr-1"></i> Password
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#billing">
<i class="mdi mdi-set-top-box mr-1"></i> Billing
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#notifications">
<i class="mdi mdi-bell-outline mr-1"></i> Notifications
</a>
</li>
</ul>
</div>
</div>
<!-- End Left Side Settings Navigation -->