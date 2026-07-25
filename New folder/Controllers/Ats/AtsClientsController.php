<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\AtsClientsMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class AtsClientsController extends Controller
{
    // LIST
    public function index()
    {
        $clients = AtsClientsMaster::latest()->paginate(10);

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

        AtsClientsMaster::create($request->all());

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Client created successfully.');
    }

    // SHOW
    public function show(AtsClientsMaster $client)
    {
        return view('ats.clients.show', compact('client'));
    }

    // EDIT
    public function edit(AtsClientsMaster $client)
    {
        return view('ats.clients.edit', compact('client'));
    }

    // UPDATE
    // UPDATE
    public function update(Request $request, AtsClientsMaster $client)
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
            'logo_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        $data = $request->except('logo_path');


        // Upload New Logo
        if ($request->hasFile('logo_path')) {

            $logo = $request->file('logo_path');

            $logoName = Str::slug($request->client_name)
                . '_' . time()
                . '.' . $logo->getClientOriginalExtension();

            $logo->storeAs(
                'Ats/clients/logo',
                $logoName,
                'public'
            );

            $data['logo_path'] = 'Ats/clients/logo/' . $logoName;
        }


        $client->update($data);


        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Client updated successfully.');
    }
    // DELETE
    public function destroy(AtsClientsMaster $client)
    {
        $client->delete();

        return redirect()
            ->route('ats.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
