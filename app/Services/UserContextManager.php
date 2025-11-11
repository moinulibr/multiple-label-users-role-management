<?php

namespace App\Services;

use App\Models\UserProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserContextManager
{
    // (Cache Keys)
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

    // A constant to represent users with no associated business in user_profiles
    const NULLABLE_BUSINESS_OF_USER_PROFILE = 'nullableBusinessOfUserProifle';

    /**
     * Set full context for the currently authenticated user based on the selected UserProfile.
     * This method is typically called upon successful login.
     *
     * @param UserProfile $profile The selected user profile (from user_profiles table).
     * @param \App\Models\User $user The authenticated user model instance.
     * @return void
     */
    public function setContext(UserProfile $profile, $user): void
    {
        $businessId = $profile->business_id ?? null;

        // Base User Session Data
        Session::put(self::SESSION_USER_ID, $user->id);
        Session::put(self::SESSION_USER_PROFILE_ID, $profile->id);
        Session::put(self::SESSION_USER_TYPE_ID, $profile->user_type_id);
        Session::put(self::SESSION_USER_TYPE, $profile->userType->name ?? null);
        Session::put(self::SESSION_BUSINESS_ID, $businessId);

        // Context Layer/Hierarchy Data
        $contextLayerId = $profile->business->hierarchy_level_id ?? 0;
        $contextValue = config("app_permissions.user_contexts_layer.{$contextLayerId}") ?? self::NULLABLE_BUSINESS_OF_USER_PROFILE;

        Session::put(self::SESSION_USER_CONTEXT_LAYER, $contextValue ?? null);
        Session::put(self::SESSION_USER_CONTEXT_LAYER_ID, $contextLayerId);

        // Flags and System Info
        Session::put(self::SESSION_IS_TENANT_USER, !is_null($businessId));
        Session::put(self::SESSION_USER_IS_DEVELOPER, $user->is_developer);

        $isSuperAdmin = $profile->userType->name == 'super_admin';
        Session::put(self::SESSION_USER_IS_SUPER_ADMIN, $isSuperAdmin);

        Session::put(self::SESSION_TIMEZONE, config('app.timezone'));
        Session::put(self::SESSION_LANGUAGE, config('app.locale'));
        Session::put(self::SESSION_SESSION_TOKEN, session()->getId());

        // Set current profile and clear related caches
        $this->setCurrentProfile($profile->id);
    }


    /**
     * Retrieves all active profiles associated with the currently authenticated user,
     * grouped by their business ID. Profiles are cached for 60 minutes.
     *
     * @return Collection<\Illuminate\Support\Collection<UserProfile>> A Collection of UserProfile models grouped by business_id.
     */
    public function getAvailableProfiles(): Collection
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $cacheKey = self::CACHED_PROFILES_KEY . ':' . $user->id;

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($user) {
            return $user->profiles()
                ->active()
                ->with(['userType', 'business'])
                ->get()
                ->groupBy('business_id'); // Grouped by Business ID
        });
    }

    /**
     * Sets the Business ID in the session.
     *
     * @param int|null $businessId The ID of the business, or null if no business context.
     * @return void
     */
    public function setBusinessId(?int $businessId = null): void
    {
        Session::put(self::SESSION_BUSINESS_ID, $businessId);
    }

    /**
     * Sets the User Type name in the session.
     *
     * @param UserProfile $profile The profile from which to extract the user type name.
     * @return void
     */
    public function setUserType(UserProfile $profile): void
    {
        Session::put(self::SESSION_USER_TYPE, $profile->userType->name ?? null);
    }

    /**
     * Sets the User Type ID and Profile ID in the session.
     *
     * @param UserProfile $profile The profile from which to extract the user type ID.
     * @return void
     */
    public function setUserTypeId(UserProfile $profile): void
    {
        Session::put(self::SESSION_USER_PROFILE_ID, $profile->id);
        Session::put(self::SESSION_USER_TYPE_ID, $profile->user_type_id);
    }

    /**
     * Determines and sets the User Context Layer (Name and ID) in the session.
     *
     * @param UserProfile $profile The profile from which to determine the context layer.
     * @return void
     */
    public function setUserContextLayer(UserProfile $profile): void
    {
        // Determine Context Layer
        $contextLayerId = $profile->business->hierarchy_level_id ?? 0;
        $contextValue = config("app_permissions.user_contexts_layer.{$contextLayerId}") ?? self::NULLABLE_BUSINESS_OF_USER_PROFILE;

        Session::put(self::SESSION_USER_CONTEXT_LAYER, $contextValue ?? null);
        Session::put(self::SESSION_USER_CONTEXT_LAYER_ID, $contextLayerId);
    }

    /**
     * Determines and sets the User Context Layer ID in the session.
     * NOTE: This method duplicates setUserContextLayer to maintain the original file's structure.
     *
     * @param UserProfile $profile The profile from which to determine the context layer.
     * @return void
     */
    public function setUserContextLayerId(UserProfile $profile): void
    {
        // Determine Context Layer
        $contextLayerId = $profile->business->hierarchy_level_id ?? 0;
        $contextValue = config("app_permissions.user_contexts_layer.{$contextLayerId}") ?? self::NULLABLE_BUSINESS_OF_USER_PROFILE;

        Session::put(self::SESSION_USER_CONTEXT_LAYER, $contextValue ?? null);
        Session::put(self::SESSION_USER_CONTEXT_LAYER_ID, $contextLayerId);
    }


    /**
     * Sets the specified UserProfile ID as the current active context in the session.
     * Also updates related session variables (Business ID, User Type, Context Layer) 
     * and clears the permission and sidebar caches for the new context.
     *
     * @param int $profileId The ID of the UserProfile to set as current.
     * @return void
     */
    public function setCurrentProfile(int $profileId): void
    {
        $profiles = $this->getAvailableProfiles()->flatten();
        /** @var UserProfile|null $profile */
        $profile = $profiles->firstWhere('id', $profileId);

        if (Auth::check() && $profile && $profile->user_id == Auth::id()) {
            Session::put(self::SESSION_USER_PROFILE_ID, $profileId);

            // Update all related session variables based on the new profile
            $this->setBusinessId($profile->business_id ?? null);
            $this->setUserType($profile);
            $this->setUserTypeId($profile);
            $this->setUserContextLayer($profile);
            $this->setUserContextLayerId($profile);

            // Clear all caches related to the old/new context
            $this->clearPermissionCache($profile);
            $this->clearSidebarMenuCache($profile);
        }
    }

    /**
     * Retrieves the currently active UserProfile model from the available profiles.
     * If no profile is set in the session, attempts to use the user's default_login profile.
     *
     * @return UserProfile|null The current active profile or null if none is found.
     */
    public function getCurrentProfile(): ?UserProfile
    {
        $currentProfileId = $this->getUserProfileId();

        // 1. If no profile is set, try to set the default_login profile
        if (!$currentProfileId && Auth::check()) {
            $defaultProfile = Auth::user()->profiles()->where('default_login', true)->first();
            if ($defaultProfile) {
                // Sets the default profile and updates $currentProfileId
                $this->setCurrentProfile($defaultProfile->id);
                $currentProfileId = $defaultProfile->id;
            }
        }

        // 2. Fetch the current profile from the cached available profiles
        if ($currentProfileId) {
            $profiles = $this->getAvailableProfiles()->flatten();
            /** @var UserProfile|null */
            return $profiles->firstWhere('id', $currentProfileId);
        }

        return null;
    }

    /**
     * Clears the cached list of available profiles for the authenticated user.
     *
     * @return void
     */
    public function clearAvailableProfilesCache(): void
    {
        if (Auth::check()) {
            // Use Auth::id() for the cache key
            Cache::forget(self::CACHED_PROFILES_KEY . ':' . Auth::id());
        }
    }

    /**
     * Generates the cache key for user permissions based on user and current context (business/profile).
     *
     * @return string The unique cache key for user permissions.
     */
    public function getPermissionCacheKey(): string
    {
        $userId = $this->getUserId();
        $businessId = $this->getBusinessId();
        $userProfileId = $this->getUserProfileId();

        // Context ID is Business ID if present, otherwise Profile ID, otherwise the NULLABLE constant
        $contextId = $businessId ?? $userProfileId ?? self::NULLABLE_BUSINESS_OF_USER_PROFILE;

        return "user_permissions:{$userId}:{$contextId}";
    }

    /**
     * Clears the permission cache for a specific UserProfile.
     *
     * @param UserProfile $profile The profile whose permission cache should be cleared.
     * @return void
     */
    public function clearPermissionCache(UserProfile $profile): void
    {
        // Use profile's actual data, not current session data
        $contextId = $profile->business_id ?? $profile->id ?? 'global';
        $cacheKey = "user_permissions:{$profile->user_id}:{$contextId}";

        Cache::forget($cacheKey);
    }

    /**
     * Generates the cache key for the sidebar menu based on user, context layer, and business/profile ID.
     *
     * @return string The unique cache key for the sidebar menu.
     */
    public function getSidebarMenuCacheKey(): string
    {
        $userId = $this->getUserId();
        $businessId = $this->getBusinessId();
        $userProfileId = $this->getUserProfileId();

        $userContext = $this->getUserContextLayer() ?? self::NULLABLE_BUSINESS_OF_USER_PROFILE;
        $contextIdentifier = $businessId ? "business:{$businessId}" : "profile:{$userProfileId}";

        return "sidebar_menu:{$userId}:{$userContext}:{$contextIdentifier}";
    }

    /**
     * Clears the sidebar menu cache for a specific UserProfile.
     *
     * @param UserProfile $profile The profile whose sidebar cache should be cleared.
     * @return void
     */
    public function clearSidebarMenuCache(UserProfile $profile): void
    {
        // The logic for context identification should ideally pull from the profile/business if available.
        // For simplicity and correctness with the existing logic:
        $userContext = $this->getUserContextLayer() ?? self::NULLABLE_BUSINESS_OF_USER_PROFILE;
        $contextIdentifier = $profile->business_id ? "business:{$profile->business_id}" : "profile:{$profile->id}";
        $cacheKey = "sidebar_menu:{$profile->user_id}:{$userContext}:{$contextIdentifier}";

        Cache::forget($cacheKey);
    }

    /**
     * Clears specific or all caches (sidebar/permission) associated with a given profile ID.
     *
     * @param int $profileId The ID of the profile to clear caches for.
     * @param string $module The cache module to clear ('sidebar', 'permission', or 'all').
     * @return void
     */
    public function clearAllCachesByProfile(int $profileId, string $module): void
    {
        if (!Auth::check()) return;

        /** @var UserProfile|null $profile */
        $profile = Auth::user()->profiles()->where('id', $profileId)->first();

        if (!$profile) return;

        switch ($module) {
            case 'sidebar':
                $this->clearSidebarMenuCache($profile);
                break;
            case 'permission':
                $this->clearPermissionCache($profile);
                break;
            case 'all':
                $this->clearSidebarMenuCache($profile);
                $this->clearPermissionCache($profile);
                break;
        }
    }

    /**
     * Retrieves all current session and cache key values for logging/debugging purposes.
     *
     * @return void
     */
    public function getAllSessionCacheAndCacheKeys(): void
    {
        // This method logs all current context values for debugging.
        // It relies on Laravel's Log facade, which is correct.
        Log::info("getUserId - " . $this->getUserId());
        Log::info("getBusinessId - " . $this->getBusinessId());
        Log::info("getUserType - " . $this->getUserType());
        Log::info("getUserTypeId - " . $this->getUserTypeId());
        Log::info("getUserProfileId - " . $this->getUserProfileId());
        Log::info("isDeveloper - " . $this->isDeveloper());
        Log::info("isSuperAdmin - " . $this->isSuperAdmin());
        Log::info("getUserContextLayer - " . $this->getUserContextLayer());
        Log::info("getUserContextLayerId - " . $this->getUserContextLayerId());
        Log::info("isTenantUser - " . $this->isTenantUser());
        Log::info("getTimezone - " . $this->getTimezone());
        Log::info("getLanguage - " . $this->getLanguage());
        Log::info("getSessionToken - " . $this->getSessionToken());
        Log::info("getAvailableProfiles - " . json_encode($this->getAvailableProfiles()));
        Log::info("getCurrentProfile - " . json_encode($this->getCurrentProfile()));
        Log::info("getPermissionCacheKey - " . json_encode($this->getPermissionCacheKey()));
        Log::info("getSidebarMenuCacheKey - " . json_encode($this->getSidebarMenuCacheKey()));
    }

    /**
     * Forgets all context-related session variables on user logout.
     *
     * @return void
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

        // It's a good practice to clear profile cache on logout
        $this->clearAvailableProfilesCache();
    }

    // 🔹 Getters (Get the current context values from the session)

    /**
     * @return int|null The ID of the currently authenticated User.
     */
    public function getUserId(): ?int
    {
        return Session::get(self::SESSION_USER_ID);
    }
    /**
     * @return int|null The ID of the current active Business (nullable).
     */
    public function getBusinessId(): ?int
    {
        return Session::get(self::SESSION_BUSINESS_ID);
    }
    /**
     * @return string|null The name (key) of the current User Type.
     */
    public function getUserType(): ?string
    {
        return Session::get(self::SESSION_USER_TYPE);
    }
    /**
     * @return int|null The ID of the current User Type.
     */
    public function getUserTypeId(): ?int
    {
        return Session::get(self::SESSION_USER_TYPE_ID);
    }
    /**
     * @return int|null The ID of the current active User Profile (user_profiles.id).
     */
    public function getUserProfileId(): ?int
    {
        return Session::get(self::SESSION_USER_PROFILE_ID);
    }
    /**
     * @return bool True if the user is marked as a developer.
     */
    public function isDeveloper(): bool
    {
        return Session::get(self::SESSION_USER_IS_DEVELOPER, false);
    }
    /**
     * @return bool True if the user's current profile is 'super_admin'.
     */
    public function isSuperAdmin(): bool
    {
        return Session::get(self::SESSION_USER_IS_SUPER_ADMIN, false);
    }
    /**
     * @return string|null The context layer string (e.g., 'global_layer', 'tenant_layer').
     */
    public function getUserContextLayer(): ?string
    {
        return Session::get(self::SESSION_USER_CONTEXT_LAYER);
    }
    /**
     * @return int|null The context layer ID (e.g., 0, 1, 2).
     */
    public function getUserContextLayerId(): ?int
    {
        return Session::get(self::SESSION_USER_CONTEXT_LAYER_ID);
    }
    /**
     * @return bool True if the current context has a Business ID set.
     */
    public function isTenantUser(): bool
    {
        return Session::get(self::SESSION_IS_TENANT_USER, false);
    }
    /**
     * @return string|null The current timezone setting.
     */
    public function getTimezone(): ?string
    {
        return Session::get(self::SESSION_TIMEZONE);
    }
    /**
     * @return string|null The current application language setting.
     */
    public function getLanguage(): ?string
    {
        return Session::get(self::SESSION_LANGUAGE);
    }
    /**
     * @return string|null The current session token (session ID).
     */
    public function getSessionToken(): ?string
    {
        return Session::get(self::SESSION_SESSION_TOKEN);
    }
}
