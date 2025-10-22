@props(['menuItems'])
{{-- 
    Blade Component (x-sidebar)
    This component renders the sidebar content - admin-layout.blade.php
--}}
<ul class="nav sidebar-inner" id="sidebar-menu">
    
    {{-- Looping on menuItems --}}
    @foreach ($menuItems as $item)

        {{-- check if item has submenu --}}
        @if (isset($item['submenu']))
            
            {{-- 1. submenu item (has-sub) --}}
            
            @php
                // active class logic: if any of submenu route is current route, then group will be active
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
                
                {{-- main dropdown link --}}
                <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#menu-{{ $menuId }}" 
                   aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="menu-{{ $menuId }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span class="nav-text">{{ __($item['title']) }}</span> 
                    <b class="caret"></b>
                </a>
                
                {{-- submenu content --}}
                <ul class="collapse {{ $isActive ? 'show' : '' }} submenu" id="menu-{{ $menuId }}" data-parent="#sidebar-menu">
                    <div class="sub-menu">
                        @foreach ($item['submenu'] as $subItem)
                            {{--every submenu item: add active class if current route --}}
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
            
            {{-- 2. Single menu item --}}
            <li class="{{ isset($item['route']) && request()->routeIs($item['route'] . '*') ? 'active' : '' }}">
                <a class="sidenav-item-link" href="{{ isset($item['route']) ? route($item['route']) : '#' }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span class="nav-text">{{ __($item['title']) }}</span>
                </a>
            </li>
        @endif

    @endforeach

</ul>
