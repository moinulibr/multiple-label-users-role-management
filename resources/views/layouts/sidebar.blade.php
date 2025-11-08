    
        
    {{-- 
        App\View\Composers\SidebarComposer 
        $menuItems
        admin-layout.blade.php - @include('layouts.sidebar')

        pp\View\Composers\SidebarComposer - $menuItems
    --}}

    {{-- @php $menu = config('sidebar'); $businessId = session('current_business_id'); @endphp
    <div class="sidebar-left" data-simplebar style="height: 100%;">
        <!-- sidebar menu -->
        <ul class="nav sidebar-inner" id="sidebar-menu">
            @foreach ($menuItems as $item)

                @if (isset($item['submenu']))
                    
                    @php
                        $isActive = false;
                        foreach ($item['submenu'] as $sub) {
                            if (isset($sub['route']) && request()->routeIs($sub['route'] . '*')) {
                                $isActive = true;
                                break;
                            }
                        }
                        $menuId = \Illuminate\Support\Str::slug($item['title']);
                    @endphp
                    
                    <li class="has-sub {{ $isActive ? 'active' : '' }}">
                        
                        <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#menu-{{ $menuId }}" 
                        aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="menu-{{ $menuId }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span class="nav-text">{{ __($item['title']) }}</span> 
                            <b class="caret"></b>
                        </a>
                        
                        <ul class="collapse {{ $isActive ? 'show' : '' }} submenu" id="menu-{{ $menuId }}" data-parent="#sidebar-menu">
                            
                            <div class="sub-menu">
                                @foreach ($item['submenu'] as $subItem)
                                    <li class="{{ isset($subItem['route']) && request()->routeIs($subItem['route']) ? 'active' : '' }}">
                                        <a class="sidenav-item-link" href="{{ isset($subItem['route']) ? route($subItem['route']) : '#' }}">
                                            <span class="nav-text">{{ __($subItem['title']) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </div>
                        </ul>
                    </li>

                @else
                    
                    <li class="{{ isset($item['route']) && request()->routeIs($item['route'] . '*') ? 'active' : '' }}">
                        <a class="sidenav-item-link" href="{{ isset($item['route']) ? route($item['route']) : '#' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span class="nav-text">{{ __($item['title']) }}</span>
                        </a>
                    </li>
                @endif

            @endforeach

        </ul>
    </div> --}}

    <div>
        <ul class="nav sidebar-inner" id="sidebar-menu">

            @foreach ($menuItems as $item)
                @php
                    $hasSub = isset($item['submenu']);
                    $isActive = $hasSub
                        ? collect($item['submenu'])->contains(fn($sub) => request()->routeIs($sub['route'] ?? ''))
                        : request()->routeIs($item['route'] ?? '');
                @endphp

                <li class="{{ $hasSub ? 'has-sub' : '' }} {{ $isActive ? 'active' : '' }}">
                    <a class="sidenav-item-link"
                    href="{{ $hasSub ? 'javascript:void(0)' : route($item['route'] ?? '#') }}"
                    @if($hasSub) data-toggle="collapse" data-target="#menu-{{ \Str::slug($item['title']) }}" @endif
                    aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                        <i class="{{ $item['icon'] ?? 'mdi mdi-circle-outline' }}"></i>
                        <span class="nav-text">{{ __($item['title']) }}</span>
                        @if($hasSub)<b class="caret"></b>@endif
                    </a>

                    @if($hasSub)
                        <ul class="collapse {{ $isActive ? 'show' : '' }} submenu" id="menu-{{ \Str::slug($item['title']) }}">
                            <div class="sub-menu">
                                @foreach ($item['submenu'] as $sub)
                                    <li class="{{ request()->routeIs($sub['route'] ?? '') ? 'active' : '' }}">
                                        <a href="{{ route($sub['route'] ?? '#') }}">
                                            <span class="nav-text">{{ __($sub['title']) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </div>
                        </ul>
                    @endif
                </li>
            @endforeach

        </ul>
    </div>
    





    {{-- @foreach($menu as $item)
        @php $perm = $item['permission'] ?? null; @endphp
        @if(!$perm || auth()->user()->hasPermission($perm, $businessId))
            <li>
                <a href="{{ route($item['route'] ?? '#') }}">{{ $item['title'] }}</a>
                @if(!empty($item['submenu']))
                    <ul>
                    @foreach($item['submenu'] as $sub)
                        @if(!isset($sub['permission']) || auth()->user()->hasPermission($sub['permission'], $businessId))
                            <li><a href="{{ route($sub['route']) }}">{{ $sub['title'] }}</a></li>
                        @endif
                    @endforeach
                    </ul>
                @endif
            </li>
        @endif
    @endforeach --}}