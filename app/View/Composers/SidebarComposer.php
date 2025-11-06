<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SidebarComposer
{
    /**
     * Load menu data and filter based on user's permissions.
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        if (!$user) {
            $view->with('menuItems', []);
            return;
        }

        $menuConfig = config('sidebar'); // your existing config
        $businessId = session('current_business_id');

        $filtered = [];
        foreach ($menuConfig as $item) {
            $mainPermission = $item['permission'] ?? null;
            $showMain = true;

            if ($mainPermission && !$user->hasPermission($mainPermission, $businessId)) {
                $showMain = false;
            }

            if (isset($item['submenu'])) {
                $subs = [];
                foreach ($item['submenu'] as $sub) {
                    $subPerm = $sub['permission'] ?? null;
                    if (!$subPerm || $user->hasPermission($subPerm, $businessId)) {
                        $subs[] = $sub;
                    }
                }
                $item['submenu'] = $subs;
                if (!empty($item['submenu'])) {
                    $filtered[] = $item;
                    continue;
                }
            }

            if ($showMain && !isset($item['submenu'])) {
                $filtered[] = $item;
            }
        }

        $view->with('menuItems', $filtered);
    }
}
