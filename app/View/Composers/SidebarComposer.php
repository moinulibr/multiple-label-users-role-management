<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\PermissionService;
use App\Services\UserContextManager;
use Illuminate\Support\Facades\Log;

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
        $userContext = $contextManager->getUserContextLayer() ?? 'nullableBusinessOfUserProifle'; // e.g. 'secondary'
        //$businessId = $contextManager->getBusinessId(); // nullable
        // cache key: per user + per context layer + per business/profile
        $cacheKey = $contextManager->getSidebarMenuCacheKey();
        // Build (and cache) filtered menu
        $menu = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($userContext) {
            $menuConfig = config('sidebar', []);
            $filtered = [];
            foreach ($menuConfig as $item) {
                if (!$this->isAllowedForContext($item, $userContext)) {
                    continue;
                }

                $mainPerm = $item['permission'] ?? null;
                $showMain = $mainPerm ? PermissionService::check($mainPerm) : true;

                // Submenu
                if (isset($item['submenu'])) {
                    $subs = [];
                    foreach ($item['submenu'] as $sub) {
                        if (!$this->isAllowedForContext($sub, $userContext)) {
                            continue;
                        }
                        $subPerm = $sub['permission'] ?? null;
                        if (!$subPerm || PermissionService::check($subPerm)) {
                            $subs[] = $sub;
                        }
                    }

                    if (!empty($subs)) {
                        $item['submenu'] = $subs;
                        $filtered[] = $item;
                        continue;
                    }

                    if ($showMain) {
                        // show main even if submenu empty (optional, choose policy)
                        $filtered[] = $item;
                    }
                } else {
                    if ($showMain) {
                        $filtered[] = $item;
                    }
                }
            }

            return $filtered;
        });
        $view->with('menuItems', $menu);
    }

    private function isAllowedForContext(array $item, string $userContext): bool
    {
        if (!empty($item['isAllowedToAllContextLayer'])) {
            return true;
        }
        $allowed = $item['contextLayer'] ?? [];
        // if no contextLayer provided, assume false (or change to true if you prefer)
        if (empty($allowed)) return false;
        return in_array($userContext, $allowed, true);
    }
   
}



/*
    When admin updates a role's permissions:
    In RoleController@update after saving:
    // update role->permissions ...
    // then find users with that role and clear caches:

    When assigning role to a user:
    After assigning:
   
    When user switches active account/business:
    Wherever your account switch logic is:
        $oldContext = $oldContextLayer;
    $oldBusiness = $oldBusinessId;

    // New context will be cached on next request automatically

*/
