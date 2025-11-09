@auth
    @inject('contextManager', 'App\Services\UserContextManager')
    
    @php
        $profiles = $contextManager->getAvailableProfiles()->flatten(); 
        $currentProfile = $contextManager->getCurrentProfile();
        $currentProfileId = $currentProfile ? $currentProfile->id : null;

        $switchableProfiles = $profiles->reject(function ($profile) use ($currentProfileId) {
            return $profile->id === $currentProfileId;
        });

        $totalSwitchable = $switchableProfiles->count();

        $currentProfileName = $currentProfile ? ($currentProfile->userType->display_name ?? $currentProfile->userType->name) : 'N/A';
        $currentBusinessName = $currentProfile ? ($currentProfile->business->name ?? 'Global') : 'N/A';
    @endphp

    <li class="dropdown-divider my-1"></li> 
    <li class="px-3 pt-1 pb-2">
        <div class="d-flex align-items-start" style="line-height: 1.2;">
            <i class="mdi mdi-account-group text-purple mr-2" style="font-size: 1.2rem; margin-top: 3px;"></i>
            
            <div class="flex-grow-1">
                <small class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem; font-weight: 600;">
                    CURRENT CONTEXT
                </small>
                
                <div style="font-size: 0.85rem; font-weight: 600;">
                    <span class="text-info">{{ $currentProfileName }}</span> 
                    <span class="text-dark d-block" style="font-size: 0.75rem; font-weight: 400;">@ {{ $currentBusinessName }}</span>
                </div>
            </div>
        </div>
    </li>
    
    @if ($totalSwitchable > 0)
        
        <li class="px-3 pt-0 pb-2 switch-container"> 
            <a data-toggle="collapse" href="#profileSwitcherList" role="button" aria-expanded="true" aria-controls="profileSwitcherList"
                class="d-flex align-items-center mb-1 bg-light p-2 rounded-sm text-decoration-none text-dark hover-bg-light profile-switcher-toggle" 
                style="line-height: 1; border: 1px solid #ccc; font-weight: bold; font-size: 0.75rem;"
                onclick="event.stopPropagation(); window.toggleBootstrapCollapse(event);">
                
                <i class="mdi mdi-swap-horizontal text-info mr-2" style="font-size: 1.2rem;"></i>
                <span class="text-uppercase flex-grow-1">
                    SWITCH TO ({{ $totalSwitchable }} MORE)
                </span>
                <i class="mdi mdi-chevron-down" style="font-size: 1rem;"></i>
            </a>

            <div class="collapse show" id="profileSwitcherList" onclick="event.stopPropagation()">
                <div class="list-group list-group-flush" style="max-height: 120px; overflow-y: auto; border: 1px solid #eee; padding: 2px 0;">
                    @foreach ($switchableProfiles as $profile)
                        @php
                            $profileLabel = $profile->userType->display_name ?? $profile->userType->name;
                            $businessName = $profile->business->name ?? 'Global';
                            $icon = 'mdi mdi-login-variant';
                        @endphp
                        
                        <a href="#" 
                           class="list-group-item list-group-item-action py-1 px-2 border-0 d-flex align-items-center text-dark hover-bg-light" 
                           style="font-size: 0.8rem; line-height: 1.2; transition: background-color 0.15s; "
                           onclick="event.preventDefault(); document.getElementById('switch-form-{{ $profile->id }}').submit(); event.stopPropagation()">
                            
                            <i class="{{ $icon }} mr-2 text-info" style="font-size: 0.9rem;"></i>
                            <span class="flex-grow-1 text-truncate">
                                <strong class="text-secondary">{{ $profileLabel }}</strong> 
                                <small class="text-muted">({{ $businessName }})</small>
                            </span>
                        </a>
                        
                        {{-- Hidden Form --}}
                        <form id="switch-form-{{ $profile->id }}" method="POST" action="{{ route('profile.switch') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                        </form>
                    @endforeach
                </div>
            </div>
        </li>
    @endif

@endauth

<script>
    window.toggleBootstrapCollapse = function(event) {
        var targetId = event.currentTarget.getAttribute('href');
        var targetElement = document.querySelector(targetId);

        if (targetElement) {
            if (targetElement.classList.contains('show')) {
                targetElement.classList.remove('show');
                event.currentTarget.setAttribute('aria-expanded', 'false');
            } else {
                targetElement.classList.add('show');
                event.currentTarget.setAttribute('aria-expanded', 'true');
            }
        }
    };
    
    document.querySelectorAll('.switch-container').forEach(function(container) {
        container.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
