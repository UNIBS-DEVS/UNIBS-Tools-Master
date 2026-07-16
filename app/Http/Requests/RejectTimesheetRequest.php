<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectTimesheetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'manager_remarks' => 'required|string|min:5|max:500',
        ];
    }

    public function messages()
    {
        return [
            'manager_remarks.required' => 'Please provide a reason for rejecting the timesheet.',
            'manager_remarks.min' => 'Rejection reason must be at least 5 characters.',
            'manager_remarks.max' => 'Rejection reason cannot exceed 500 characters.',
        ];
    }
}
