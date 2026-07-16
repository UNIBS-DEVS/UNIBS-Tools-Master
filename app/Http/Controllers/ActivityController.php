<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function index()
    {
        $projects = Project::where('status', 'active')
            ->orderBy('name')
            ->get();

        $activities = Activity::with('project')
            ->latest()
            ->paginate(10);

        return view('activities.index', compact('activities', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'status'     => 'required|in:active,inactive',
        ], [
            'name.required'       => 'Activity name is required.',
            'name.unique'         => 'This activity already exists.',
            'project_id.required' => 'Please select a project.',
            'status.required'     => 'Please select activity status.',
        ]);

        Activity::create($validated);

        return redirect()
            ->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('activities', 'name')->ignore($activity->id),
            ],
            'project_id' => 'required|exists:projects,id',
            'status'     => 'required|in:active,inactive',
        ], [
            'name.required'       => 'Activity name is required.',
            'name.unique'         => 'This activity already exists.',
            'project_id.required' => 'Please select a project.',
            'status.required'     => 'Please select activity status.',
        ]);

        $activity->update($validated);

        return redirect()
            ->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }
}
