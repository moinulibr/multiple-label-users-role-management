<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('users.create');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'nullable|boolean',
            // profiles is optional array to create profiles with user
            'profiles' => 'nullable|array',
            'profiles.*.user_type_id' => 'required_with:profiles|exists:user_types,id',
            'profiles.*.business_id' => 'nullable|exists:businesses,id',
            // role assignment
            'assign_roles' => 'nullable|array',
            'assign_roles.*' => 'integer|exists:roles,id',
            'profile_id' => 'nullable|integer|exists:user_profiles,id'
        ];
    }
}
