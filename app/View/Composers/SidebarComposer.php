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

        // 1. If the user is not authenticated, return an empty menu
        if (!$user) {
            $view->with('menuItems', []);
            return;
        }

        // config('sidebar') data load from config
        $menuConfig = config('sidebar');
        $filteredMenu = [];

        foreach ($menuConfig as $item) {
            $mainPermission = $item['permission'] ?? null;
            $canSeeMainItem = true;

            // check main permission exists
            if ($mainPermission && !$user->can($mainPermission)) {
                $canSeeMainItem = false;
            }

            //2. handle submenu
            if (isset($item['submenu'])) {
                $filteredSubmenu = [];

                //filter every submenu
                foreach ($item['submenu'] as $subItem) {
                    $subPermission = $subItem['permission'] ?? null;

                    if (!$subPermission || $user->can($subPermission)) {
                        $filteredSubmenu[] = $subItem;
                    }
                }

                $item['submenu'] = $filteredSubmenu;

                // if there is only one item in the submenu, show the main menu
                if (!empty($item['submenu'])) {
                    $filteredMenu[] = $item;
                    continue; // Go to the next iteration
                }
            }

            //3. if single item exists and permission exists, then added 
            if ($canSeeMainItem && !isset($item['submenu'])) {
                $filteredMenu[] = $item;
            }
        }

        $view->with('menuItems', $filteredMenu);
    }
}
