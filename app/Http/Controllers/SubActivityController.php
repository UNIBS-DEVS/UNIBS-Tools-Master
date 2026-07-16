<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\SubActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubActivityController extends Controller
{
    public function index()
    {
        $subActivities = SubActivity::with([
            'project',
            'activity'
        ])
            ->latest()
            ->paginate(5);

        $projects = Project::where('status', 'active')
            ->orderBy('name')
            ->get();

        $activities = Activity::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('sub_activities.index', compact(
            'subActivities',
            'projects',
            'activities'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'project_id'  => 'required|exists:projects,id',
            'activity_id' => 'required|exists:activities,id',
            'status'      => 'required|in:active,inactive',
        ], [
            'name.required'        => 'Sub Activity name is required.',
            'name.unique'          => 'This sub activity already exists.',
            'project_id.required'  => 'Please select a project.',
            'activity_id.required' => 'Please select an activity.',
            'status.required'      => 'Please select status.',
        ]);

        SubActivity::create($validated);

        return redirect()
            ->route('sub-activities.index')
            ->with('success', 'Sub Activity created successfully.');
    }

    public function update(Request $request, SubActivity $subActivity)
    {
        $validated = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'activity_id' => 'required|exists:activities,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_activities', 'name')->ignore($subActivity->id),
            ],
            'status' => 'required|in:active,inactive',
        ], [
            'name.required'        => 'Sub Activity name is required.',
            'name.unique'          => 'This sub activity already exists.',
            'project_id.required'  => 'Please select a project.',
            'activity_id.required' => 'Please select an activity.',
            'status.required'      => 'Please select status.',
        ]);

        $subActivity->update($validated);

        return redirect()
            ->route('sub-activities.index')
            ->with('success', 'Sub Activity updated successfully.');
    }
}
