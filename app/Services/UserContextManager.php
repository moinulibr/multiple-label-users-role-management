<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserContextManager
{
    // (Session Keys)
    const CURRENT_PROFILE_ID_KEY = 'current_user_profile_id';
    const CACHED_PROFILES_KEY = 'user_profiles_cache';

    // 🔹 Session keys
    const SESSION_BUSINESS_ID = 'business_id';
    const SESSION_USER_TYPE = 'user_type';
    const SESSION_USER_TYPE_ID = 'user_type_id';
    const SESSION_USER_ID = 'user_id';
    const SESSION_USER_PROFILE_ID = 'user_profile_id';
    const SESSION_USER_IS_DEVELOPER = 'user_is_developer';
    const SESSION_USER_IS_SUPER_ADMIN = 'user_is_super_admin';
    const SESSION_USER_CONTEXT_LAYER = 'user_contexts_layer';
    const SESSION_USER_CONTEXT_LAYER_ID = 'user_contexts_layer_id';
    const SESSION_IS_TENANT_USER = 'is_tenant_user';
    const SESSION_TIMEZONE = 'timezone';
    const SESSION_LANGUAGE = 'language';
    const SESSION_SESSION_TOKEN = 'session_token';

    //user_profiles table [business_id nullable]
    const NULLABLE_BUSINESS_OF_USER_PROFILE = 'nullableBusinessOfUserProifle';//all of the users, who has no business_id in the user_profiles table [business_id nullable]


    /**
     * ✅ Set full context for current user
     */
    public function setContext(UserProfile $profile, $user): void
    {
        $businessId = $profile->business_id;

        Session::put(self::SESSION_USER_ID, $user->id);
        Session::put(self::SESSION_USER_PROFILE_ID, $profile->id);
        Session::put(self::SESSION_USER_TYPE_ID, $profile->user_type_id);
        Session::put(self::SESSION_USER_TYPE, $profile->userType->name ?? null);
        Session::put(self::SESSION_BUSINESS_ID, $businessId);
        $contextLayerId = $profile->business->hierarchy_level_id ?? 0;
        Log::info("session - context layer id - " . $contextLayerId);
        $contextValue = config("app_permissions.user_contexts_layer.{$contextLayerId}") ?? self::NULLABLE_BUSINESS_OF_USER_PROFILE;
        Log::info("session - context layer value - " . $contextValue);
        Session::put(self::SESSION_USER_CONTEXT_LAYER, $contextValue ?? null);
        Session::put(self::SESSION_USER_CONTEXT_LAYER_ID, $contextLayerId);
        Session::put(self::SESSION_IS_TENANT_USER, !is_null($businessId));
        Session::put(self::SESSION_USER_IS_DEVELOPER, $user->is_developer);
        $isSuperAdmin = $profile->userType->name == 'super_admin' ? true : false;
        Session::put(self::SESSION_USER_IS_SUPER_ADMIN, $isSuperAdmin);
        Session::put(self::SESSION_TIMEZONE, config('app.timezone'));
        Session::put(self::SESSION_LANGUAGE, config('app.locale'));
        Session::put(self::SESSION_SESSION_TOKEN, session()->getId());

        //$this->getAvailableProfiles();
        $this->setCurrentProfile($profile->id);
    }


    /**
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableProfiles()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $cacheKey = self::CACHED_PROFILES_KEY . ':' . $this->getUserProfileId();

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($user) {
            return $user->profiles()
                ->active()
                ->with(['userType', 'business'])
                ->get()
                ->groupBy('business_id'); // Business ID
        });
    }

    /**
     * @param int $profileId
     * @return void
     */
    public function setCurrentProfile(int $profileId): void
    {
        Session::forget([self::SESSION_USER_PROFILE_ID]);
        $profiles = $this->getAvailableProfiles()->flatten();
        $profile = $profiles->firstWhere('id', $profileId);

        /*  Log::info("session - avaliable profiles - " . json_encode($profiles) );
            Log::info("session - avaliable profiles flatten - " . json_encode($this->getAvailableProfiles()->flatten()) );
            Log::info("session - single profile - " . json_encode($profiles->firstWhere('id', $profileId)) );
            Log::info("session - current profile id - " . $profileId);
        */

        if (Auth::check() && $profile && $profile->user_id == Auth::id()) {
            Session::put(self::SESSION_USER_PROFILE_ID, $profileId);
            // clear permission cache
            $this->clearPermissionCache($profile);
        }
    }

    /**
     *
     * @return \App\Models\UserProfile|null
     */
    public function getCurrentProfile(): ?UserProfile
    {  
        //$currentProfileId = Session::get(self::SESSION_USER_PROFILE_ID);
        $currentProfileId = $this->getUserProfileId();

        if (!$currentProfileId) {
            $defaultProfile = Auth::user()->profiles()->where('default_login', true)->first();
            if ($defaultProfile) {
                $this->setCurrentProfile($defaultProfile->id);
                $currentProfileId = $defaultProfile->id;
            }
        }

        if ($currentProfileId) {
            $profiles = $this->getAvailableProfiles()->flatten();
            return $profiles->firstWhere('id', $currentProfileId);
        }

        return null;
    }

    /**
     *
     * @return void
     */
    public function clearAvailableProfilesCache(): void
    {
        if (Auth::check()) {
            Cache::forget(self::CACHED_PROFILES_KEY . ':' . $this->getUserProfileId());
        }
    }



    public function setPermissionCache(){
        $userProfileId = $this->getUserProfileId();
        $businessId = $this->getBusinessId();
        $userId = $this->getUserId();
        $contextId = $businessId ?? $userProfileId ?? 'global';
        return "user_permissions:{$userId}:{$contextId}";
    }
    public function getPermissionCacheKey(){
        return $this->setPermissionCache();
    }
    /**
     *
     * @param UserProfile $profile
     * @return void
     */
    public function clearPermissionCache(UserProfile $profile): void
    {
        $contextId = $profile->business_id ?? $profile->id ?? 'global';
        $cacheKey = "user_permissions:{$profile->user_id}:{$contextId}";

        Cache::forget($cacheKey);
    }

    public function setSidebarMenuCache()
    {
        $userProfileId = $this->getUserProfileId();
        $businessId = $this->getBusinessId();
        $userId = $this->getUserId();
       
        $userContext = $this->getUserContextLayer() ?? 'nullableBusinessOfUserProifle';
        $contextIdentifier = $businessId ? "business:{$businessId}" : "profile:{$userProfileId}";
        return "sidebar_menu:{$userId}:{$userContext}:{$contextIdentifier}";
    }
    public function getSidebarMenuCacheKey()
    {
        return $this->setSidebarMenuCache();
    }
    public function clearSidebarMenuCache(UserProfile $profile): void
    {
        $userContext = $this->getUserContextLayer() ?? 'nullableBusinessOfUserProifle';
        $contextIdentifier = $profile->business_id ? "business:{$profile->business_id}" : "profile:{$profile->id}";
        $cacheKey =  "sidebar_menu:{$profile->user_id}:{$userContext}:{$contextIdentifier}";

        Cache::forget($cacheKey);
    }

    public function clearAllCachesByProfile($profileId, $module){
        $profile = Auth::user()->profiles()->where('id', $profileId)->first();
        if($profile && $module == 'sidebar'){
            $this->clearSidebarMenuCache($profile);
        }
        else if($profile && $module == 'permission'){
            $this->clearPermissionCache($profile);
        }
        else if($profile && $module == 'all'){
            $this->clearSidebarMenuCache($profile);
            $this->clearPermissionCache($profile);
        }
    }

    /**
     * ✅ Forget context on logout
     */
    public function forgetContext(): void
    {
        Session::forget([
            self::SESSION_USER_ID,
            self::SESSION_USER_PROFILE_ID,
            self::SESSION_USER_TYPE_ID,
            self::SESSION_USER_TYPE,
            self::SESSION_BUSINESS_ID,
            self::SESSION_IS_TENANT_USER,
            self::SESSION_USER_IS_DEVELOPER,
            self::SESSION_USER_IS_SUPER_ADMIN,
            self::SESSION_TIMEZONE,
            self::SESSION_LANGUAGE,
            self::SESSION_SESSION_TOKEN,
            self::SESSION_USER_CONTEXT_LAYER,
            self::SESSION_USER_CONTEXT_LAYER_ID,
        ]);
    }

    // 🔹 Getters
    public function getUserId(): ?int
    {
        return Session::get(self::SESSION_USER_ID);
    }
    public function getBusinessId(): ?int
    {
        return Session::get(self::SESSION_BUSINESS_ID);
    }
    public function getUserType(): ?string
    {
        return Session::get(self::SESSION_USER_TYPE);
    }
    public function getUserTypeId(): ?int
    {
        return Session::get(self::SESSION_USER_TYPE_ID);
    }
    public function getUserProfileId(): ?int
    {
        return Session::get(self::SESSION_USER_PROFILE_ID);
    }
    public function isDeveloper(): bool
    {
        return Session::get(self::SESSION_USER_IS_DEVELOPER, false);
    }
    public function isSuperAdmin(): bool
    {
        return Session::get(self::SESSION_USER_IS_SUPER_ADMIN, false);
    }
    public function getUserContextLayer()
    {
        return Session::get(self::SESSION_USER_CONTEXT_LAYER);
    }
    public function getUserContextLayerId():?int
    {
        return Session::get(self::SESSION_USER_CONTEXT_LAYER_ID);
    }
    public function isTenantUser(): bool
    {
        return Session::get(self::SESSION_IS_TENANT_USER, false);
    }
    public function getTimezone(): ?string
    {
        return Session::get(self::SESSION_TIMEZONE);
    }
    public function getLanguage(): ?string
    {
        return Session::get(self::SESSION_LANGUAGE);
    }
    public function getSessionToken(): ?string
    {
        return Session::get(self::SESSION_SESSION_TOKEN);
    }
}
