<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\AtsClientMaster;
use App\Models\AtsClientsSysConfig;
use Illuminate\Http\Request;

class AtsClientSysConfigController extends Controller
{
    // CREATE FORM
    public function create(AtsClientMaster $client)
    {
        $existing = AtsClientsSysConfig::where('client_id', $client->id)->first();

        if ($existing) {
            return redirect()
                ->route('ats.clientsSysConfigs.edit', $client)
                ->with('info', 'Configuration already exists.');
        }

        return view('ats.clients_sys_config.form', [
            'config' => new AtsClientsSysConfig(),
            'client' => $client,
            'isEdit' => false
        ]);
    }

    // STORE
    public function store(Request $request, AtsClientMaster $client)
    {
        $validated = $request->validate([
            'support_user' => 'nullable|string|max:255',
            'support_password' => 'nullable|string|max:255',

            'db_host' => 'required|string|max:255',
            'db_mysql_port' => 'required|integer|between:1,65535',
            'db_name' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',

            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_auth' => 'required|in:tls,ssl',

            'graph_client_id' => 'nullable|string|max:255',
            'graph_tenant_id' => 'nullable|string|max:255',
            'graph_client_secret_id' => 'nullable|string|max:255',
            'graph_client_secret_value' => 'nullable|string|max:255',
            'graph_redirect_url' => 'nullable|string|max:255',
            'graph_client_expiry_date' => 'nullable|date', 

            'resume_parse_email' => 'nullable|email',
            'resume_parsing_time' => 'nullable|string',

            'login_auth_type' => 'required|in:basic,oauth',
            'email_auth_type' => 'required|in:smtp,graph_id',
        ]);

        // Convert comma string to array
        $times = array_filter(array_map('trim', explode(',', $request->resume_parsing_time)));

        foreach ($times as $time) {
            if (!preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                return back()->withErrors([
                    'resume_parsing_time' => 'Invalid time format. Use H:MM or HH:MM (e.g. 1:00 or 14:05)'
                ])->withInput();
            }
        }

        $validated['resume_parsing_time'] = $times;

        $validated['client_id'] = $client->id;

        AtsClientsSysConfig::create($validated);

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Configuration created successfully.');
    }

    // EDIT
    public function edit(AtsClientMaster $client)
    {
        $config = AtsClientsSysConfig::where('client_id', $client->id)->firstOrFail();

        return view('ats.clients_sys_config.form', [
            'config' => $config,
            'client' => $client,
            'isEdit' => true
        ]);
    }

    // UPDATE
    public function update(Request $request, AtsClientMaster $client)
    {
        $config = AtsClientsSysConfig::where('client_id', $client->id)->firstOrFail();

        $validated = $request->validate([
            'support_user' => 'nullable|string|max:255',
            'support_password' => 'nullable|string|max:255',

            'db_host' => 'required|string|max:255',
            'db_mysql_port' => 'required|integer|between:1,65535',
            'db_name' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',

            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_auth' => 'required|in:tls,ssl',

            'graph_client_id' => 'nullable|string|max:255',
            'graph_tenant_id' => 'nullable|string|max:255',
            'graph_client_secret_id' => 'nullable|string|max:255',
            'graph_client_secret_value' => 'nullable|string|max:255',
            'graph_redirect_url' => 'nullable|string|max:255',
            'graph_client_expiry_date' => 'nullable|date',
 
            'resume_parse_email' => 'nullable|email',
            'resume_parsing_time' => 'nullable|string',

            'login_auth_type' => 'required|in:basic,oauth',
            'email_auth_type' => 'required|in:smtp,graph_id',
        ]);

        // Convert comma string to array
        $times = array_filter(array_map('trim', explode(',', $request->resume_parsing_time)));

        foreach ($times as $time) {
            if (!preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                return back()->withErrors([
                    'resume_parsing_time' => 'Invalid time format. Use H:MM or HH:MM (e.g. 1:00 or 14:05)'
                ])->withInput();
            }
        }

        $validated['resume_parsing_time'] = $times;

        if (!$request->filled('db_password')) {
            unset($validated['db_password']);
        }

        if (!$request->filled('support_password')) {
            unset($validated['support_password']);
        }

        $config->update($validated);

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Configuration updated successfully.');
    }

    // DELETE
    public function destroy(AtsClientMaster $client)
    {
        $config = AtsClientsSysConfig::where('client_id', $client->id)->first();

        if ($config) {
            $config->delete();
        }

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Configuration deleted successfully.');
    }
}
