<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('roles.manage');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'display_name' => 'nullable|string|max:150',
            'business_id' => 'nullable|exists:businesses,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_special' => 'nullable|boolean',
        ];
    }
}
