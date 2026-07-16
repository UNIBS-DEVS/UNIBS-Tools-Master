<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\AtsClientMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AtsClientController extends Controller
{
    // LIST
    public function index()
    {
        $clients = AtsClientMaster::latest()->paginate(10);

        return view('ats.clients.index', compact('clients'));
    }

    // CREATE FORM
    public function create()
    {
        return view('ats.clients.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'client_code' => 'required|unique:ats_clients_master,client_code',
            'client_name' => 'required|string|max:150',
            'status' => 'required|in:active,inactive',
            'client_spoc_email' => 'nullable|email',
            'client_spoc_mobile' => 'nullable|digits_between:10,15',
        ]);

        AtsClientMaster::create($request->all());

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Client created successfully.');
    }

    // SHOW
    public function show(AtsClientMaster $client)
    {
        return view('ats.clients.show', compact('client'));
    }

    // EDIT
    public function edit(AtsClientMaster $client)
    {
        return view('ats.clients.edit', compact('client'));
    }

    // UPDATE
    public function update(Request $request, AtsClientMaster $client)
    {
        $request->validate([
            'client_code' => [
                'required',
                Rule::unique('ats_clients_master', 'client_code')
                    ->ignore($client->id),
            ],
            'client_name' => 'required|string|max:150',
            'status' => 'required|in:active,inactive',
            'client_spoc_email' => 'nullable|email',
            'client_spoc_mobile' => 'nullable|digits_between:10,15',
        ]);

        $client->update($request->all());

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    // DELETE
    public function destroy(AtsClientMaster $client)
    {
        $client->delete();

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
