<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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

            'email' => 'required|email|unique:users',

            'personal_mobile' => 'required|max:20',

            'offical_mobile' => 'required|max:20',

            'roles' => 'required|array',

            'roles.*' => 'in:admin,manager,api user,accounts,customer,db inspection',

            'status' => 'required|in:active,inactive',

            'manager_id' => 'required|exists:users,id',

            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
