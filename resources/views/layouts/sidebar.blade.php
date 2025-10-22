    
        
    {{-- 
        App\View\Composers\SidebarComposer 
        $menuItems
        admin-layout.blade.php - @include('layouts.sidebar')

        pp\View\Composers\SidebarComposer - $menuItems
    --}}

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
    </div>
