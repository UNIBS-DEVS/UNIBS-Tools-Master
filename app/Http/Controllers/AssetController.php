<?php

namespace App\Http\Controllers;

use App\Models\AssetMaster;
use App\Models\AssetCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /* ================= LIST ================= */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'accounts', 'manager'])) {
            abort(403, 'Only admins and managers are allowed');
        }

        $assets = AssetMaster::with(['currentAllocation.employee', 'currentRepair'])->latest()->paginate(10);
        return view('asset.index', compact('assets'));
    }

    /* ================= CREATE FORM ================= */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'accounts', 'manager'])) {
            abort(403, 'Only admins and managers are allowed');
        }

        $categories = AssetCategory::pluck('category_name')->toArray();
        $vendors = Vendor::all();

        return view('asset.create', compact('categories', 'vendors'));
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'accounts, manager'])) {
            abort(403, 'Only admins and managers are allowed');
        }

        $request->validate([
            'AssetMaster_category' => 'required|string|max:50',
            'AssetMaster_number'   => 'required|string|max:20',
            'asset_name'           => 'required|string|max:50',
            'serial_number'        => 'nullable|integer',
            'brand_name'           => 'required|string|max:50',
            'model_number'         => 'required|integer',
            'vendor'               => 'required|exists:vendors,id',
            'purchase_date'        => 'nullable|date',
            'purchase_cost'        => 'nullable|numeric',
            'warranty_expiry_date' => 'nullable|date',
            'status'               => 'required|string'
        ]);

        AssetMaster::create([
            'asset_category'       => $request->AssetMaster_category,
            'asset_code'           => $request->AssetMaster_number,
            'asset_name'           => $request->asset_name,
            'serial_number'        => $request->serial_number,
            'brand_name'           => $request->brand_name,
            'model_number'         => $request->model_number,
            'vendor_id'            => $request->vendor,
            'purchase_date'        => $request->purchase_date ?: now()->toDateString(),
            'purchase_cost'        => $request->purchase_cost ?: 0,
            'status'               => $request->status,
            'warranty_expiry_date' => $request->warranty_expiry_date ?: now()->toDateString(),
            'created_by'           => Auth::id(),
            'updated_by'           => Auth::id()
        ]);

        return redirect()
            ->route('asset.index')
            ->with('success', 'Asset Added Successfully');
    }

    /* ================= SHOW ================= */
    public function show(AssetMaster $asset)
    {
        if (!Auth::user()->hasRole(['admin', 'accounts'])) {
            abort(403, 'Only admins are allowed');
        }

        $asset->load([
            'vendorRelation',
            'currentAllocation.employee',
            'allocations.employee',
            'repairs.vendor',
            'recharges',
            'documents'
        ]);
        return view('asset.show', compact('asset'));
    }

    /* ================= EDIT ================= */
    public function edit(AssetMaster $asset)
    {
        if (!Auth::user()->hasRole(['admin', 'accounts', 'manager'  ])) {
            abort(403, 'Only admins and managers are allowed');
        }

        // $AssetMaster = $asset;
        $categories = AssetCategory::pluck('category_name')->toArray();
        $vendors = Vendor::all();

        // return view('asset.edit', compact('AssetMaster', 'categories', 'vendors'));
        
        return view('asset.edit', compact('asset', 'categories', 'vendors'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, AssetMaster $asset)
    {
        if (!Auth::user()->hasRole(['admin', 'accounts', 'manager'])) {
            abort(403, 'Only admins are allowed');
        }


        $request->validate([
            'AssetMaster_category' => 'required|string|max:50',
            'AssetMaster_number'   => 'required|string|max:20',
            'asset_name'           => 'required|string|max:50',
            'serial_number'        => 'nullable|integer',
            'brand_name'           => 'required|string|max:50',
            'model_number'         => 'required|integer',
            'vendor'               => 'required|exists:vendors,id',
            'purchase_date'        => 'nullable|date',
            'purchase_cost'        => 'nullable|numeric',
            'warranty_expiry_date' => 'nullable|date',
            'status'               => 'required|string'
        ]);

         $asset->update([
            'asset_category'       => $request->AssetMaster_category,
            'asset_code'           => $request->AssetMaster_number,
            'asset_name'           => $request->asset_name,
            'serial_number'        => $request->serial_number,
            'brand_name'           => $request->brand_name,
            'model_number'         => $request->model_number,
            'vendor_id'            => $request->vendor,
            'purchase_date'        => $request->purchase_date ?: now()->toDateString(),
            'purchase_cost'        => $request->purchase_cost ?: 0,
            'status'               => $request->status,
            'warranty_expiry_date' => $request->warranty_expiry_date ?: now()->toDateString(),
            'updated_by'           => Auth::id()
        ]);

        return redirect()
            ->route('asset.index')
            ->with('success', 'Asset Updated Successfully');
    }

    /* ================= DELETE ================= */
    public function destroy(AssetMaster $asset)
    {
        if (!Auth::user()->hasRole(['admin', 'accounts', 'manager'])) {
            abort(403, 'Only admins and managers are allowed');
        }

        $asset->delete();

        return redirect()
            ->route('asset.index')
            ->with('success', 'Asset Deleted Successfully');
    }
}