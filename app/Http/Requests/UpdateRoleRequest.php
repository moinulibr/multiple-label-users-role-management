<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('roles.manage');
    }

    public function rules()
    {
        return [
            'display_name' => 'nullable|string|max:150',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_special' => 'nullable|boolean',
        ];
    }
}
