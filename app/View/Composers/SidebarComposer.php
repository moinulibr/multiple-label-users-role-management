<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Services\UserContextManager;
use App\Services\PermissionService;

class SidebarComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        if (!$user) {
            $view->with('menuItems', []);
            return;
        }

        $contextManager = app(UserContextManager::class);
        $userContext = $contextManager->getUserContextLayer(); // e.g. 'secondary'
        $menuConfig = config('sidebar');

        $filteredMenu = [];

        foreach ($menuConfig as $item) {
            if (!$this->isAllowedForContext($item, $userContext)) {
                continue;
            }

            $mainPerm = $item['permission'] ?? null;
            $showMain = $mainPerm ? PermissionService::check($mainPerm) : true;

            // Submenu filter
            if (isset($item['submenu'])) {
                $subItems = [];
                foreach ($item['submenu'] as $sub) {
                    if (!$this->isAllowedForContext($sub, $userContext)) {
                        continue;
                    }

                    $subPerm = $sub['permission'] ?? null;
                    if (!$subPerm || PermissionService::check($subPerm)) {
                        $subItems[] = $sub;
                    }
                }

                if (!empty($subItems)) {
                    $item['submenu'] = $subItems;
                    $filteredMenu[] = $item;
                } elseif ($showMain) {
                    $filteredMenu[] = $item;
                }
            } else {
                if ($showMain) {
                    $filteredMenu[] = $item;
                }
            }
        }

        $view->with('menuItems', $filteredMenu);
    }

    /**
     * 🔹 Check if menu is allowed for current user context
     */
    private function isAllowedForContext(array $item, string $userContext): bool
    {
        if (!empty($item['isAllowedToAllContextLayer'])) {
            return true;
        }

        $allowed = $item['contextLayer'] ?? [];
        return in_array($userContext, $allowed);
    }
}
