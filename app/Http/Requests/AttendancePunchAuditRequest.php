<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendancePunchAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'device_id' => ['required', 'string'],

            'device_time' => ['nullable', 'date'],

            'action' => [
                'nullable',
                'in:punch_in,punch_out,auto_punch_in,auto_punch_out'
            ],

            'skip_reason' => ['nullable', 'string'],

            'punch_success' => ['nullable', 'boolean'],
            'is_checked_in' => ['nullable', 'boolean'],

            'active_location_id' => ['nullable', 'integer'],
            'eligible_location_id' => ['nullable', 'integer'],

            'token_found' => ['nullable', 'boolean'],
            'local_state_used' => ['nullable', 'boolean'],
            'server_state_fetched' => ['nullable', 'boolean'],

            'position_error' => ['nullable', 'string'],
            'gps_error' => ['nullable', 'string'],
            'locations_api_error' => ['nullable', 'string'],

            'position_lat' => ['nullable', 'numeric'],
            'position_lng' => ['nullable', 'numeric'],
            'position_accuracy_m' => ['nullable', 'numeric'],
            'position_source' => ['nullable', 'string'],

            'exception' => ['nullable', 'string'],
        ];
    }
}
