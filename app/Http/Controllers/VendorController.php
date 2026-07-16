<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    /* ================= LIST ================= */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Only admins are allowed');
        }

        $vendors = Vendor::latest()->paginate(10);
        return view('vendors.index', compact('vendors'));
    }

    /* ================= CREATE FORM ================= */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Only admins are allowed');
        }

        return view('vendors.create');
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Only admins are allowed');
        }

        $request->validate([
            'vendor_name'    => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:100',
            'email'          => 'nullable|email|max:200',
            'mobile_no'      => 'nullable|string|max:20',
            'gst'            => 'nullable|string|max:30',
        ]);

        Vendor::create([
            'vendor_name'    => $request->vendor_name,
            'contact_person' => $request->contact_person,
            'email'          => $request->email,
            'mobile_no'      => $request->mobile_no,
            'gst'            => $request->gst,
            'created_by'     => Auth::id(),
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor Created Successfully');
    }

    /* ================= EDIT FORM ================= */
    public function edit(string $id)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Only admins are allowed');
        }

        $vendor = Vendor::findOrFail($id);
        return view('vendors.edit', compact('vendor'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, string $id)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Only admins are allowed');
        }

        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'vendor_name'    => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:100',
            'email'          => 'nullable|email|max:200',
            'mobile_no'      => 'nullable|string|max:20',
            'gst'            => 'nullable|string|max:30',
        ]);

        $vendor->update([
            'vendor_name'    => $request->vendor_name,
            'contact_person' => $request->contact_person,
            'email'          => $request->email,
            'mobile_no'      => $request->mobile_no,
            'gst'            => $request->gst,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor Updated Successfully');
    }

    /* ================= DELETE ================= */
    public function destroy(string $id)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Only admins are allowed');
        }

        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor Deleted Successfully');
    }
}
