<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\AtsClientsMaster;
use App\Models\AtsClientsSysConfig;
use App\Models\ClientModule;
use App\Models\Module;
use Illuminate\Http\Request;

class AtsClientsSysConfigController extends Controller
{
    // CREATE FORM
    public function create(AtsClientsMaster $client)
    {
        $config = AtsClientsSysConfig::firstOrNew([
            'client_id' => $client->id
        ]);

        $isEdit = $config->exists;

        // Get all modules of UNIONE Application (Application ID = 3)
        $modules = Module::with('application')
            ->where('app_id', 1)
            ->orderBy('name')
            ->get();

        // Already selected modules
        $selectedModules = ClientModule::where('client_id', $client->id)
            ->pluck('module_id')
            ->toArray();

        return view('ats.clients_sys_config.form', compact(
            'client',
            'config',
            'isEdit',
            'modules',
            'selectedModules'
        ));
    }

    // STORE
    public function store(Request $request, AtsClientsMaster $client)
    {
        $validated = $this->validateData($request);

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

        // Save Modules
        if ($request->filled('modules')) {

            foreach ($request->modules as $moduleId) {

                $module = Module::findOrFail($moduleId);

                ClientModule::create([
                    'client_id' => $client->id,
                    'app_id' => $module->app_id,
                    'module_id' => $moduleId,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        }

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Configuration saved successfully.');
    }

    // EDIT
    // public function edit(AtsClientsMaster $client)
    // {
    //     $config = AtsClientsSysConfig::where('client_id', $client->id)->firstOrFail();

    //     return view('ats.clients_sys_config.form', [
    //         'config' => $config,
    //         'client' => $client,
    //         'isEdit' => true
    //     ]);
    // }

    // UPDATE
    public function update(Request $request, AtsClientsMaster $client)
    {
        $config = AtsClientsSysConfig::where('client_id', $client->id)->firstOrFail();

        $validated = $this->validateData($request);

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

        // Remove old modules
        ClientModule::where('client_id', $client->id)->delete();

        // Save selected modules
        if ($request->filled('modules')) {

            foreach ($request->modules as $moduleId) {

                $module = Module::findOrFail($moduleId);

                ClientModule::create([
                    'client_id' => $client->id,
                    'app_id' => $module->app_id,
                    'module_id' => $moduleId,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        }

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Configuration updated successfully.');
    }

    // DELETE
    public function destroy(AtsClientsMaster $client)
    {
        $config = AtsClientsSysConfig::where('client_id', $client->id)->first();

        if ($config) {
            $config->delete();
        }

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Configuration deleted successfully.');
    }


    private function validateData(Request $request)
    {
        return $request->validate(
            [
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

                'modules' => 'nullable|array',
                'modules.*' => 'exists:modules,id',
            ]
        );
    }
}
