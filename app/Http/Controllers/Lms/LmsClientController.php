<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsClientsMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LmsClientController extends Controller
{
    // LIST
    public function index()
    {
        $clients = LmsClientsMaster::latest()->paginate(10);

        return view('lms.clients.index', compact('clients'));
    }

    // CREATE FORM
    public function create()
    {
        return view('lms.clients.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'client_code' => 'required|unique:lms_clients_master,client_code',
            'client_name' => 'required|string|max:150',
            'status' => 'required|in:active,inactive',
            'client_spoc_email' => 'nullable|email',
            'client_spoc_mobile' => 'nullable|digits_between:10,15',
        ]);

        LmsClientsMaster::create($request->all());

        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Client created successfully.');
    }

    // SHOW
    public function show(LmsClientsMaster $client)
    {
        return view('lms.clients.show', compact('client'));
    }

    // EDIT
    public function edit(LmsClientsMaster $client)
    {
        return view('lms.clients.edit', compact('client'));
    }

    // UPDATE
    public function update(Request $request, LmsClientsMaster $client)
    {
        $request->validate([
            'client_code' => [
                'required',
                Rule::unique('lms_clients_master', 'client_code')
                    ->ignore($client->id),
            ],
            'client_name' => 'required|string|max:150',
            'status' => 'required|in:active,inactive',
            'client_spoc_email' => 'nullable|email',
            'client_spoc_mobile' => 'nullable|digits_between:10,15',
        ]);

        $client->update($request->all());

        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    // DELETE
    public function destroy(LmsClientsMaster $client)
    {
        $client->delete();

        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
