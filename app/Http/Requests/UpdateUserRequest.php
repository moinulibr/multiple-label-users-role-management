<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('users.edit');
    }

    public function rules()
    {
        $userId = $this->route('user')?->id ?? null;
        return [
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|boolean',
            'profiles' => 'nullable|array',
            'profiles.*.id' => 'nullable|exists:user_profiles,id',
            'profiles.*.user_type_id' => 'required_with:profiles|exists:user_types,id',
            'profiles.*.business_id' => 'nullable|exists:businesses,id',
        ];
    }
}
