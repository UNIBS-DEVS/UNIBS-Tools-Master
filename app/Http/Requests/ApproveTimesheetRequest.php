<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveTimesheetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'manager_remarks' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'manager_remarks.max' => 'Remarks cannot exceed 500 characters.',
        ];
    }
}
