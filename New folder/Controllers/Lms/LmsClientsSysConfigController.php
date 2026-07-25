<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\ClientModule;
use App\Models\LmsClientsMaster;
use App\Models\LmsClientsSysConfig;
use App\Models\Module;
use Illuminate\Http\Request;

class LmsClientsSysConfigController extends Controller
{
    // CREATE FORM
    public function create(LmsClientsMaster $client)
    {
        $config = LmsClientsSysConfig::firstOrNew([
            'client_id' => $client->id
        ]);

        $isEdit = $config->exists;

        // Get all modules of UNIONE Application (Application ID = 3)
        $modules = Module::with('application')
            ->where('app_id', 2)
            ->orderBy('name')
            ->get();

        // Already selected modules
        $selectedModules = ClientModule::where('client_id', $client->id)
            ->pluck('module_id')
            ->toArray();

        return view('lms.clients_sys_config.form', compact(
            'client',
            'config',
            'isEdit',
            'modules',
            'selectedModules'
        ));
    }

    // STORE
    public function store(Request $request, LmsClientsMaster $client)
    {
        $validated = $this->validateData($request);

        $validated['client_id'] = $client->id;

        $config = LmsClientsSysConfig::create($validated);

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
            ->route('lms.clients.index')
            ->with('success', 'Configuration saved successfully.');
    }

    // UPDATE
    public function update(Request $request, LmsClientsMaster $client)
    {
        $config = LmsClientsSysConfig::where('client_id', $client->id)->firstOrFail();

        $validated = $this->validateData($request);

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
            ->route('lms.clients.index')
            ->with('success', 'Configuration updated successfully.');
    }

    // DELETE
    public function destroy(LmsClientsMaster $client)
    {
        ClientModule::where('client_id', $client->id)->delete();

        LmsClientsSysConfig::where('client_id', $client->id)->delete();

        return redirect()
            ->route('lms.clients.index')
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
