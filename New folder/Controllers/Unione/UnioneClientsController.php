<?php

namespace App\Http\Controllers\Unione;

use App\Http\Controllers\Controller;
use App\Models\UnioneClientsMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'client_code'        => 'required|unique:unione_clients_master,client_code',
            'client_name'        => 'required|string|max:150',
            'status'             => 'required|in:active,inactive',
            'status_message'     => 'nullable|string|max:500',
            'client_spoc_email'  => 'nullable|email',
            'client_spoc_mobile' => 'nullable|digits_between:10,15',
            'logo_path' => 'nullable|file|mimes:jpg,jpeg,png',
        ]);

        $data = $request->except('logo_path');

        // Upload Logo
        if ($request->hasFile('logo_path')) {

            $logo = $request->file('logo_path');

            $logoName = Str::slug($request->client_name)
                . '_' . time()
                . '.' . $logo->getClientOriginalExtension();

            $logo->storeAs(
                'unione/clients/logo',
                $logoName,
                'public'
            );

            $data['logo_path'] = 'unione/clients/logo/' . $logoName;
        }

        UnioneClientsMaster::create($data);

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
                Rule::unique('unione_clients_master', 'client_code')
                    ->ignore($client->id),
            ],
            'client_name'        => 'required|string|max:150',
            'status'             => 'required|in:active,inactive',
            'status_message'     => 'nullable|string|max:500',
            'client_spoc_email'  => 'nullable|email',
            'client_spoc_mobile' => 'nullable|digits_between:10,15',
            'logo_path' => 'nullable|file|mimes:jpg,jpeg,png',
        ]);

        $data = $request->except('logo_path');

        // Upload New Logo
        if ($request->hasFile('logo_path')) {

            // Delete Old Logo
            if ($client->logo_path && Storage::disk('public')->exists($client->logo_path)) {
                Storage::disk('public')->delete($client->logo_path);
            }

            $logo = $request->file('logo_path');

            $logoName = Str::slug($request->client_name)
                . '_' . time()
                . '.' . $logo->getClientOriginalExtension();

            $logo->storeAs(
                'unione/clients/logo',
                $logoName,
                'public'
            );

            $data['logo_path'] = 'unione/clients/logo/' . $logoName;
        }

        $client->update($data);

        return redirect()
            ->route('unione.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    // DELETE
    public function destroy(UnioneClientsMaster $client)
    {
        // Delete Logo
        if ($client->logo_path && Storage::disk('public')->exists($client->logo_path)) {
            Storage::disk('public')->delete($client->logo_path);
        }

        $client->delete();

        return redirect()
            ->route('unione.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
