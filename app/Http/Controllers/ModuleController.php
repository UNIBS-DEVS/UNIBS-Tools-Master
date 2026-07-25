<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = Module::with('application');

        if ($request->filled('app_id')) {
            $query->where('app_id', $request->app_id);
        }

        $modules = $query->latest()->paginate(10);

        $applications = Application::orderBy('appName')->get();

        $application = null;

        if ($request->filled('app_id')) {
            $application = Application::find($request->app_id);
        }

        return view('modules.index', compact('modules', 'applications', 'application'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // dd("ljk");
        $application = Application::findOrFail($request->app_id);

        return view('modules.create', compact('application'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'app_id' => 'required|exists:applications,id',
            'name'   => 'required|string|max:255',
        ]);

        Module::create([
            'app_id'     => $request->app_id,
            'name'       => $request->name,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('modules.index', ['app_id' => $request->app_id])
            ->with('success', 'Module created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        $module->load('application');

        return view('modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        $module->load('application');

        return view('modules.edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'app_id'         => 'required|exists:applications,id',
            // 'moduleCode'     => 'required|string|max:50|unique:modules,moduleCode,' . $module->id,
            'name'           => 'required|string|max:255',
            // 'status'         => 'required|boolean',
            // 'status_message' => 'nullable|string|max:255',
        ]);

        $module->update([
            'app_id'         => $request->app_id,
            // 'moduleCode'     => $request->moduleCode,
            'name'           => $request->name,
            // 'status'         => $request->status,
            // 'status_message' => $request->status_message,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module deleted successfully.');
    }
}
