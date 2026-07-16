<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Project name is required.',
            'name.unique'   => 'This project already exists.',

            'status.required' => 'Please select project status.',
        ]);

        Project::create($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('projects', 'name')->ignore($project->id),
            ],
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Project name is required.',
            'name.unique'   => 'This project already exists.',

            'status.required' => 'Please select project status.',
        ]);

        $project->update($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }
}
