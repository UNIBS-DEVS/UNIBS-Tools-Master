<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Application::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('appCode', 'like', "%{$search}%")
                    ->orWhere('appName', 'like', "%{$search}%")
                    ->orWhere('status_message', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(10);

        return view('applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('applications.create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'appCode' => 'required|string|max:50|unique:applications,appCode',
            'appName' => 'required|string|max:255',
            'status_message' => 'nullable|string|max:255',
        ]);

        Application::create([
            'appCode' => $request->appCode,
            'appName' => $request->appName,
            'status' => $request->has('status') ? 1 : 0,
            'status_message' => $request->status_message,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('applications.index')
            ->with('success', 'Application created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return view('applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        return view('applications.edit', compact('application'));
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Application $application)
    {
        $request->validate([
            'appCode' => 'required|string|max:50|unique:applications,appCode,' . $application->id,
            'appName' => 'required|string|max:255',
            'status_message' => 'nullable|string|max:255',
        ]);

        $application->update([
            'appCode' => $request->appCode,
            'appName' => $request->appName,
            'status' => $request->has('status') ? 1 : 0,
            'status_message' => $request->status_message,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('applications.index')
            ->with('success', 'Application updated successfully.');
    }
    /**
     * Remove the specified resource.
     */
    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()
            ->route('applications.index')
            ->with('success', 'Application deleted successfully.');
    }
}
