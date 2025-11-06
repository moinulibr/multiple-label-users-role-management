<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\UserProfile;

class UserContextManager
{
    // 🔹 Session keys
    const SESSION_BUSINESS_ID = 'business_id';
    const SESSION_USER_TYPE = 'user_type';
    const SESSION_USER_TYPE_ID = 'user_type_id';
    const SESSION_USER_ID = 'user_id';
    const SESSION_USER_PROFILE_ID = 'user_profile_id';
    const SESSION_USER_IS_DEVELOPER = 'user_is_developer';
    const SESSION_USER_IS_SUPER_ADMIN = 'user_is_super_admin';
    const SESSION_USER_CONTEXT_LAYER = 'user_contexts_layer';
    const SESSION_IS_TENANT_USER = 'is_tenant_user';
    const SESSION_TIMEZONE = 'timezone';
    const SESSION_LANGUAGE = 'language';
    const SESSION_SESSION_TOKEN = 'session_token';

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
        Session::put(self::SESSION_IS_TENANT_USER, !is_null($businessId));
        Session::put(self::SESSION_USER_IS_DEVELOPER, $user->is_developer);
        $isSuperAdmin = $profile->userType->name == 'super_admin' ? true : false;
        Session::put(self::SESSION_USER_IS_SUPER_ADMIN, $isSuperAdmin);
        Session::put(self::SESSION_TIMEZONE, config('app.timezone'));
        Session::put(self::SESSION_LANGUAGE, config('app.locale'));
        Session::put(self::SESSION_SESSION_TOKEN, session()->getId());
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
