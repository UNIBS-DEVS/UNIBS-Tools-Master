<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')->id),
            ],

            'personal_mobile' => 'required|max:20',
            'offical_mobile' => 'required|max:20',
            'roles' => 'required|array',
            'roles.*' => 'in:admin,manager,api user,accounts,customer,db inspection',
            'status' => 'required|in:active,inactive',
            'manager_id' => 'required|exists:users,id',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
