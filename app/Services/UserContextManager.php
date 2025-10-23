<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Session;

class UserContextManager
{
    const SESSION_BUSINESS_ID = 'current_business_id';
    const SESSION_USER_TYPE_ID = 'active_user_type_id';
    const SESSION_IS_TENANT_USER = 'is_tenant_user';

    /**
     * বর্তমান ব্যবহারকারীর সক্রিয় বিজনেস কনটেক্সট সেট করে।
     */
    public function setContext(UserProfile $profile): void
    {
        $businessId = $profile->business_id;

        Session::put(self::SESSION_USER_TYPE_ID, $profile->user_type_id);
        Session::put(self::SESSION_BUSINESS_ID, $businessId);
        Session::put(self::SESSION_IS_TENANT_USER, !is_null($businessId));
    }

    /**
     * সক্রিয় বিজনেস ID রিটার্ন করে।
     */
    public function getCurrentBusinessId(): ?int
    {
        return Session::get(self::SESSION_BUSINESS_ID);
    }

    /**
     * সক্রিয় User Type ID রিটার্ন করে।
     */
    public function getActiveUserTypeId(): ?int
    {
        return Session::get(self::SESSION_USER_TYPE_ID);
    }

    /**
     * ইউজার কি একটি বিজনেসের কর্মচারী/ম্যানেজার?
     */
    public function isTenantUser(): bool
    {
        return Session::get(self::SESSION_IS_TENANT_USER, false);
    }

    /**
     * সকল কনটেক্সট তথ্য রিসেট করে (লগআউটের সময়)।
     */
    public function forgetContext(): void
    {
        Session::forget([
            self::SESSION_BUSINESS_ID,
            self::SESSION_USER_TYPE_ID,
            self::SESSION_IS_TENANT_USER,
        ]);
    }
}
