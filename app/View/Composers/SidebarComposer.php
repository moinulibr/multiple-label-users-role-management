<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\PermissionService;
use App\Services\UserContextManager;

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
        $businessId = $contextManager->getBusinessId(); // nullable
        $contextIdentifier = $businessId ? "business:{$businessId}" : "profile:{$contextManager->getUserProfileId()}";

        // cache key: per user + per context layer + per business/profile
        $cacheKey = "sidebar_menu:{$user->id}:{$userContext}:{$contextIdentifier}";

        // Build (and cache) filtered menu
        $menu = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($user, $userContext, $businessId) {
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

    /**
     * Clear cached menu entries for a given user (optionally for a context and business)
     * Use this helper from controllers when role/permission changes or account switch happens.
     */
    public static function clearMenuCacheForUser($userId, $contextLayer = null, $businessId = null, $userProfileId = null)
    {
        $context = $contextLayer ?? 'any';
        $identifier = $businessId ? "business:{$businessId}" : ($userProfileId ? "profile:{$userProfileId}" : 'any');
        // If 'any' used, you might need to iterate potential context layers; keep it simple: delete likely keys:
        // Examples to clear:
        $keys = [
            "sidebar_menu:{$userId}:{$context}:{$identifier}",
            // Also clear generic keys if needed
        ];
        foreach ($keys as $k) {
            \Illuminate\Support\Facades\Cache::forget($k);
        }
    }
}

/*
    When admin updates a role's permissions:
    In RoleController@update after saving:
    // update role->permissions ...
    // then find users with that role and clear caches:
    $users = $role->users; // relation via role_user
    foreach ($users as $u) {
        $u->clearPermissionCache($role->business_id ?? null);
        \App\Http\View\Composers\SidebarComposer::clearMenuCacheForUser($u->id, null, $role->business_id, $u->user_profile_id);
    }


    When assigning role to a user:
    After assigning:
    $user->clearPermissionCache($businessId);
    \App\Http\View\Composers\SidebarComposer::clearMenuCacheForUser($user->id, $userContext, $businessId, $user->user_profile_id);


    When user switches active account/business:
    Wherever your account switch logic is:
        $oldContext = $oldContextLayer;
    $oldBusiness = $oldBusinessId;

    $u = Auth::user();
    \App\Http\View\Composers\SidebarComposer::clearMenuCacheForUser($u->id, $oldContext, $oldBusiness, $u->user_profile_id);
    // optionally clear permission cache for old context
    $u->clearPermissionCache($oldBusiness);

    // New context will be cached on next request automatically

*/
