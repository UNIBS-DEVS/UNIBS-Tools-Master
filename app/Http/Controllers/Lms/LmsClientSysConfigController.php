<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsClientMaster;
use App\Models\LmsClientsSysConfig;
use Illuminate\Http\Request;

class LmsClientSysConfigController extends Controller
{
    // CREATE FORM
    public function create(LmsClientMaster $client)
    {
        $existing = LmsClientsSysConfig::where('client_id', $client->id)->first();

        if ($existing) {
            return redirect()
                ->route('lms.clientsSysConfigs.edit', $client)
                ->with('info', 'Configuration already exists.');
        }

        return view('lms.clients_sys_config.form', [
            'config' => new LmsClientsSysConfig(),
            'client' => $client,
            'isEdit' => false
        ]);
    }

    // STORE
    public function store(Request $request, LmsClientMaster $client)
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

            'login_auth_type' => 'required|in:basic,oauth',
            'email_auth_type' => 'required|in:smtp,graph_id',
        ]);


        $validated['client_id'] = $client->id;

        LmsClientsSysConfig::create($validated);

        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Configuration created successfully.');
    }

    // EDIT
    public function edit(LmsClientMaster $client)
    {
        $config = LmsClientsSysConfig::where('client_id', $client->id)->firstOrFail();

        return view('lms.clients_sys_config.form', [
            'config' => $config,
            'client' => $client,
            'isEdit' => true
        ]);
    }

    // UPDATE
    public function update(Request $request, LmsClientMaster $client)
    {
        $config = LmsClientsSysConfig::where('client_id', $client->id)->firstOrFail();

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

            'login_auth_type' => 'required|in:basic,oauth',
            'email_auth_type' => 'required|in:smtp,graph_id',
        ]);



        if (!$request->filled('db_password')) {
            unset($validated['db_password']);
        }

        if (!$request->filled('support_password')) {
            unset($validated['support_password']);
        }

        $config->update($validated);

        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Configuration updated successfully.');
    }

    // DELETE
    public function destroy(LmsClientMaster $client)
    {
        $config = LmsClientsSysConfig::where('client_id', $client->id)->first();

        if ($config) {
            $config->delete();
        }

        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Configuration deleted successfully.');
    }
}
