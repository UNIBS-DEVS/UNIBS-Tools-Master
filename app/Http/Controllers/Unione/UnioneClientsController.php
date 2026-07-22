<?php

namespace App\Http\Controllers\Unione;

use App\Http\Controllers\Controller;
use App\Models\UnioneClientsMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnioneClientsController extends Controller
{
    // LIST
    public function index()
    {
        $clients = UnioneClientsMaster::latest()->paginate(10);

        return view('unione.clients.index', compact('clients'));
    }

    // CREATE FORM
    public function create()
    {
        return view('unione.clients.create');
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

        UnioneClientsMaster::create($request->all());

        return redirect()
            ->route('unione.clients.index')
            ->with('success', 'Client created successfully.');
    }

    // SHOW
    public function show(UnioneClientsMaster $client)
    {
        return view('unione.clients.show', compact('client'));
    }

    // EDIT
    public function edit(UnioneClientsMaster $client)
    {
        return view('unione.clients.edit', compact('client'));
    }

    // UPDATE
    public function update(Request $request, UnioneClientsMaster $client)
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
            ->route('unione.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    // DELETE
    public function destroy(UnioneClientsMaster $client)
    {
        $client->delete();

        return redirect()
            ->route('unione.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
