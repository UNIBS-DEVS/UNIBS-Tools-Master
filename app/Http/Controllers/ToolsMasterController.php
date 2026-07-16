<?php

namespace App\Http\Controllers;

use App\Models\ToolsMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ToolsMasterController extends Controller
{
    public function edit()
    {
        $tool = ToolsMaster::first();

        if (!$tool) {
            $tool = ToolsMaster::create([]);
        }

        return view('tools_master.edit', compact('tool'));
    }


    public function update(Request $request)
    {
        // dd($request->all());
        $tool = ToolsMaster::firstOrFail();

        $validated = $request->validate([
            'support_user' => 'nullable|string|max:255',
            'support_password' => 'nullable|string|max:255',

            'hr_email' => 'nullable|string|max:255',
            'accounts_email' => 'nullable|string|max:255',

            'attendance_notification_email' => 'nullable|string|max:255',
            'timesheet_notification_email' => 'nullable|string|max:255',
            'call_review_notification_email' => 'nullable|string|max:255',

            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_auth' => 'required|in:tls,ssl',

            'graph_client_id' => 'nullable|string|max:255',
            'graph_tenant_id' => 'nullable|string|max:255',
            'graph_client_secret_id' => 'nullable|string|max:255',
            'graph_client_secret_value' => 'nullable|string|max:255',
            'graph_redirect_url' => 'nullable|string|max:255',
            'graph_client_expiry_date' => 'nullable|date',

            'login_auth_type' => 'required|in:basic,oauth',
            'email_auth_type' => 'required|in:smtp,graph_id',
        ]);




        $tool->update($validated);

        return redirect()
            ->route('tools-master.edit')
            ->with('success', 'Configuration updated successfully.');
    }
}
