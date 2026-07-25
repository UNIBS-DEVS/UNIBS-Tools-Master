<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsClientsMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LmsClientsController extends Controller
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
        // dd($request->all());
        $request->validate([
            'client_code' => 'required|unique:lms_clients_master,client_code',
            'client_name' => 'required|string|max:150',
            'status' => 'required|in:active,inactive',
            'status_message' => 'nullable|string|max:500',
            'client_spoc_email' => 'nullable|email',
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
                'clients/logo',
                $logoName,
                'public'
            );


            $data['logo_path'] = 'clients/logo/' . $logoName;
        }


        LmsClientsMaster::create($data);


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
        // dd($request->all());
        $request->validate([
            'client_code' => [
                'required',
                Rule::unique('lms_clients_master', 'client_code')
                    ->ignore($client->id),
            ],
            'client_name' => 'required|string|max:150',
            'status' => 'required|in:active,inactive',
            'status_message' => 'nullable|string|max:500',
            'client_spoc_email' => 'nullable|email',
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
                'Lms/clients/logo',
                $logoName,
                'public'
            );


            $data['logo_path'] = 'Lms/clients/logo/' . $logoName;
        }


        $client->update($data);


        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Client updated successfully.');
    }


    // DELETE
    public function destroy(LmsClientsMaster $client)
    {
        // Delete Logo
        if ($client->logo_path && Storage::disk('public')->exists($client->logo_path)) {
            Storage::disk('public')->delete($client->logo_path);
        }

        $client->delete();


        return redirect()
            ->route('lms.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
