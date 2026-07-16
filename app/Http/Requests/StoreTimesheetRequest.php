<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimesheetRequest extends FormRequest
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
            'tasks' => 'required|array',

            'tasks.*.*.project_id.*' => 'nullable|exists:projects,id',
            'tasks.*.*.activity_id.*' => 'nullable|exists:activities,id',
            'tasks.*.*.sub_activity_id.*' => 'nullable|exists:sub_activities,id',

            'tasks.*.*.hours.*' => 'required|numeric|min:0.25|max:24',

            'tasks.*.*.remarks.*' => 'nullable|string|max:255',

            'user_remarks' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'week_start.required' => 'Please select a week',
            'tasks.required' => 'Please add at least one task',
            'tasks.*.hours.*.required' => 'Hours are required',
            'tasks.*.hours.*.min' => 'Hours must be at least 0.25',
        ];
    }
}
