@auth
    @inject('contextManager', 'App\Services\UserContextManager')
    @php
        $profiles = $contextManager->getAvailableProfiles();
        $currentProfile = $contextManager->getCurrentProfile();
        $currentProfileId = $currentProfile ? $currentProfile->id : null;

        $currentProfileName = $currentProfile 
                              ? ($currentProfile->userType->display_name ?? $currentProfile->userType->name)
                              : 'Select Profile';
        $currentBusinessName = $currentProfile 
                               ? ($currentProfile->business->name ?? 'Global')
                               : '';

        $totalProfiles = $profiles->flatten()->count();
    @endphp

    @if ($totalProfiles > 1)
        
        <li class="dropdown-divider my-1"></li> 

        <li class="px-3">
            <div style="background-color:#E8ECCF;padding-bottom: 1px;">
                <div class="d-flex align-items-center mb-1" style="line-height: 1;">
                    <i class="mdi mdi-account-group text-primary mr-0" style="font-size: 1rem;"></i>
                    <div class="flex-grow-1" style="background-color:#fff;padding:3px 1px; padding-left:3px;z-index: 111111;">
                        <small class="d-block text-muted" style="line-height: 1; font-size: 0.7rem;">Current Context</small>
                        <strong class="d-block text-primary" style="line-height: 1; font-size: 0.9rem;">
                        <strong style="color:green"> {{ $currentProfileName }} </strong>
                            <br>
                        <small style="color:black">{{ $currentBusinessName }}</small>
                        </strong>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mt-1 mb-1" style="line-height: 1;">
                    <i class="mdi mdi-swap-horizontal text-info mr-0" style="font-size: 20px;"></i>
                    <span class="text-uppercase text-dark" style="font-size: 0.7rem; letter-spacing: 0.5px; font-weight: bold;">
                        SWITCH TO ({{ $totalProfiles - 1 }} More)
                    </span>
                </div>
            </div>
            <div class="list-group list-group-flush" style="max-height:70px; overflow-y: auto;">
                @foreach ($profiles as $businessId => $profileGroup)
                    @foreach ($profileGroup as $profile)
                        @php
                            $isSelected = $currentProfileId === $profile->id;
                            $profileLabel = $profile->userType->display_name ?? $profile->userType->name;
                            $businessName = $profile->business->name ?? 'Global';

                            if ($isSelected) {
                                continue;
                            }
                            
                            $icon = 'mdi mdi-login-variant';
                        @endphp
                        
                        <a href="#" 
                           class="list-group-item list-group-item-action py-0 px-1 border-0 rounded-sm mb-1 d-flex align-items-center text-dark hover-bg-light" 
                           style="font-size: 0.8rem; line-height: 1.6; transition: background-color 0.15s; "
                           onclick="event.preventDefault(); document.getElementById('switch-form-{{ $profile->id }}').submit();">
                            
                            <i class="{{ $icon }} mr-2 text-info" style="font-size: 1rem;"></i>
                            <span class="flex-grow-1">
                                {{ $profileLabel }} <small class="text-muted">({{ $businessName }})</small>
                            </span>
                        </a>
                        
                        <form id="switch-form-{{ $profile->id }}" method="POST" action="{{ route('profile.switch') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                        </form>
                    @endforeach
                @endforeach
            </div>
        </li>
       
    @endif

@endauth




{{-- @auth
    @inject('contextManager', 'App\Services\UserContextManager')
    @php
        $profiles = $contextManager->getAvailableProfiles();
        $currentProfile = $contextManager->getCurrentProfile();
        $currentProfileId = $currentProfile ? $currentProfile->id : null;

        $currentProfileName = $currentProfile 
                              ? ($currentProfile->userType->display_name ?? $currentProfile->userType->name)
                              : 'Select Profile';
        $currentBusinessName = $currentProfile 
                               ? ($currentProfile->business->name ?? 'Global')
                               : '';

        $totalProfiles = $profiles->flatten()->count();
    @endphp

    @if ($totalProfiles > 1)
        
        <li class="dropdown-divider my-1"></li> 

        <li class="px-3 pt-2 pb-1">
            
            <div class="d-flex align-items-center mb-2">
                <i class="mdi mdi-account-group text-primary mr-2" style="font-size: 1.2rem;"></i>
                <div class="flex-grow-1">
                    
                    <small class="d-block text-muted" style="line-height: 1; font-size: 0.7rem;">Current Context</small>
                    <strong class="d-block text-primary" style="line-height: 1; font-size: 0.9rem;">
                        {{ $currentProfileName }} @ {{ $currentBusinessName }}
                    </strong>
                </div>
            </div>
            
            <div class="d-flex align-items-center mb-1">
                <i class="mdi mdi-swap-horizontal text-info mr-2" style="font-size: 1rem;"></i>
                <span class="text-uppercase text-dark" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: bold;">
                    Switch To ({{ $totalProfiles - 1 }} More)
                </span>
            </div>

           
            <div class="list-group list-group-flush" style="max-height: 150px; overflow-y: auto;">
                @foreach ($profiles as $businessId => $profileGroup)
                    @foreach ($profileGroup as $profile)
                        @php
                            $isSelected = $currentProfileId === $profile->id;
                            $profileLabel = $profile->userType->display_name ?? $profile->userType->name;
                            $businessName = $profile->business->name ?? 'Global';

                            // বর্তমান প্রোফাইলকে তালিকা থেকে বাদ দিন, কারণ এটি ইতিমধ্যেই উপরে প্রদর্শিত
                            if ($isSelected) {
                                continue;
                            }
                            
                            $icon = 'mdi mdi-login-variant';
                        @endphp
                        
                        <a href="#" 
                           class="list-group-item list-group-item-action py-1 px-2 border-0 rounded-sm mb-1 d-flex align-items-center text-dark hover-bg-light" 
                           style="font-size: 0.85rem; line-height: 1.2; transition: background-color 0.15s;"
                           onclick="event.preventDefault(); document.getElementById('switch-form-{{ $profile->id }}').submit();">
                            
                            <i class="{{ $icon }} mr-2 text-info" style="font-size: 1rem;"></i>
                            <span class="flex-grow-1">
                                {{ $profileLabel }} <small class="text-muted">({{ $businessName }})</small>
                            </span>
                        </a>
                        
                        <form id="switch-form-{{ $profile->id }}" method="POST" action="{{ route('profile.switch') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                        </form>
                    @endforeach
                @endforeach
            </div>
        </li>
       
        <li class="dropdown-divider my-1"></li> 
    @endif
@endauth --}}



{{-- @inject('contextManager', 'App\Services\UserContextManager')
@php
    $profiles = $contextManager->getAvailableProfiles();
    $currentProfile = $contextManager->getCurrentProfile();
    $currentProfileId = $currentProfile ? $currentProfile->id : null;
@endphp


@if ($profiles->flatten()->count() > 1)
<div style="border:1px solid #ccc;margin:2px;">
    
    
    <li class="dropdown-header py-2 d-flex align-items-center">
        <i class="mdi mdi-swap-horizontal-bold text-info mr-2" style="font-size: 1.2rem;"></i> 
        <strong class="text-uppercase text-dark" style="font-size: 0.8rem; letter-spacing: 0.5px;">Profile Switcher</strong>
    </li>
    
    
    <li class="px-3 pt-0 pb-1"> 
        <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
            
            @foreach ($profiles as $businessId => $profileGroup)
                @php
                    // বিজনেসের নাম
                    $businessName = $profileGroup->first()->business->name ?? 'Global/Personal Profile';
                @endphp
                
                
                <div class="mt-2 mb-1">
                    <div class="d-flex align-items-center">
                        <span class="text-dark font-weight-bold flex-grow-1" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.8;">
                            {{ $businessName }}
                        </span>
                       
                        <hr class="flex-grow-1 my-0 ml-2" style="border-top: 1px solid #ddd;">
                    </div>
                </div>
                
                @foreach ($profileGroup as $profile)
                    @php
                        $isSelected = $currentProfileId === $profile->id;
                        $profileLabel = $profile->userType->display_name ?? $profile->userType->name;
                        
                        // স্টাইল: নির্বাচিত প্রোফাইলের জন্য bg-primary, অন্যদের জন্য হালকা ব্যাকগ্রাউন্ড/টেক্সট
                        $linkClass = $isSelected 
                                    ? 'text-white bg-primary shadow-sm' 
                                    : 'text-secondary hover-bg-light';
                        
                        $icon = $isSelected ? 'mdi mdi-check-circle' : 'mdi mdi-account-cog-outline';
                    @endphp
                    
                    <a href="#" 
                       class="list-group-item list-group-item-action py-1 px-2 border-0 rounded-sm mb-1 d-flex align-items-center {{ $linkClass }}" 
                       style="font-size: 0.85rem; line-height: 1.2; transition: background-color 0.15s;"
                       onclick="event.preventDefault(); document.getElementById('switch-form-{{ $profile->id }}').submit();">
                        
                        <i class="{{ $icon }} mr-2" style="font-size: 1rem;"></i>
                        <span class="flex-grow-1" style="{{ $isSelected ? 'color: white;' : '' }}">
                            {{ $profileLabel }}
                        </span>
                        
                        @if ($isSelected)
                            <span class="badge badge-light badge-pill ml-auto" style="font-size: 0.7rem;">Active</span>
                        @endif
                    </a>
                    
                    
                    <form id="switch-form-{{ $profile->id }}" method="POST" action="{{ route('profile.switch') }}" style="display: none;">
                        @csrf
                        <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                    </form>
                @endforeach
            @endforeach
        </div>
    </li>
   </div>
@endif --}}


{{--     @inject('contextManager', 'App\Services\UserContextManager')
    @php
        $profiles = $contextManager->getAvailableProfiles();
        $currentProfile = $contextManager->getCurrentProfile();
        $currentProfileId = $currentProfile ? $currentProfile->id : null;
    @endphp

    @if ($profiles->count() > 1 || $profiles->first() && $profiles->first()->count() > 1)
        <li class="dropdown-header">
            <strong class="text-uppercase text-muted">Switch Profile</strong>
        </li>
        
        <li class="p-2 pt-0 pb-1">
            <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                @foreach ($profiles as $businessId => $profileGroup)
                    @php
                        // বিজনেসের নাম: যদি business_id না থাকে, তবে এটি ব্যক্তিগত বা গ্লোবাল প্রোফাইল
                        $businessName = $profileGroup->first()->business->name ?? 'Global/Personal Profile';
                    @endphp
                    
                    <h6 class="dropdown-header pt-2 pb-1 text-primary" style="font-size: 0.85rem;">
                        {{ $businessName }}
                    </h6>
                    
                    @foreach ($profileGroup as $profile)
                        @php
                            $isSelected = $currentProfileId === $profile->id;
                            $profileLabel = $profile->userType->display_name ?? $profile->userType->name;
                            $linkClass = $isSelected ? 'text-success font-weight-bold active' : 'text-secondary';
                            $icon = $isSelected ? 'mdi mdi-check-circle-outline text-success' : 'mdi mdi-circle-outline';
                        @endphp
                        
                        <a href="#" class="list-group-item list-group-item-action py-1 {{ $isSelected ? 'active-profile' : '' }}" 
                            style="font-size: 0.9rem;"
                            onclick="event.preventDefault(); document.getElementById('switch-form-{{ $profile->id }}').submit();">
                            <i class="{{ $icon }} mr-1"></i>
                            {{ $profileLabel }} 
                            @if ($isSelected)
                                <span class="float-right badge badge-success badge-pill">Active</span>
                            @endif
                        </a>
                        
                        <form id="switch-form-{{ $profile->id }}" method="POST" action="{{ route('profile.switch') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                        </form>
                    @endforeach
                @endforeach
            </div>
        </li>
        <li class="dropdown-divider"></li>
    @endif --}}