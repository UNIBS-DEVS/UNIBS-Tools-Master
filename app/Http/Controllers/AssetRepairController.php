<?php

namespace App\Http\Controllers;

use App\Models\AssetRepair;
use App\Models\AssetMaster;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetRepairController extends Controller
{
    /* ================= LIST ================= */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        return redirect()->route('asset.index');
    }

    /* ================= CREATE FORM ================= */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $assets = AssetMaster::orderBy('asset_name')->get();
        $vendors = Vendor::orderBy('vendor_name')->get();

        return view('asset_repairs.create', compact('assets', 'vendors'));
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'asset_id'          => 'required|exists:asset_masters,id',
            'vendor_id'         => 'nullable|exists:vendors,id',
            'issue_description' => 'nullable|string|max:1000',
            'reported_date'     => 'nullable|date',
            'sent_date'         => 'nullable|date',
            'repair_cost'       => 'nullable|numeric',
            'repair_status'     => 'required|string|max:50',
            'remarks'           => 'nullable|string|max:500'
        ]);

        $repair = AssetRepair::create([
            'asset_id'          => $request->asset_id,
            'vendor_id'         => $request->vendor_id,
            'issue_description' => $request->issue_description,
            'reported_date'     => $request->reported_date,
            'sent_date'         => $request->sent_date,
            'repair_cost'       => $request->repair_cost,
            'repair_status'     => $request->repair_status,
            'remarks'           => $request->remarks,
            'created_by'        => Auth::id(),
            'updated_by'        => Auth::id()
        ]);

        // If status is "Sent for Repair" or "Under Repair", update asset master status to "under repair"
        $status = strtolower($request->repair_status);
        if (in_array($status, ['sent for repair', 'under repair', 'reported'])) {
            AssetMaster::where('id', $request->asset_id)->update([
                'status' => 'under repair'
            ]);
        }

        return redirect()
            ->route('asset-repairs.index')
            ->with('success', 'Asset repair logged successfully.');
    }

    /* ================= SHOW ================= */
    public function show(AssetRepair $repair)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $repair->load(['asset', 'vendor']);

        return view('asset_repairs.show', compact('repair'));
    }

    /* ================= EDIT FORM ================= */
    public function edit(AssetRepair $repair)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $assets = AssetMaster::orderBy('asset_name')->get();
        $vendors = Vendor::orderBy('vendor_name')->get();

        return view('asset_repairs.edit', compact('repair', 'assets', 'vendors'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, AssetRepair $repair)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'asset_id'          => 'required|exists:asset_masters,id',
            'vendor_id'         => 'nullable|exists:vendors,id',
            'issue_description' => 'nullable|string|max:1000',
            'reported_date'     => 'nullable|date',
            'sent_date'         => 'nullable|date',
            'received_date'     => 'nullable|date',
            'repair_cost'       => 'nullable|numeric',
            'repair_status'     => 'required|string|max:50',
            'remarks'           => 'nullable|string|max:500'
        ]);

        $status = strtolower($request->repair_status);
        $receivedDate = $request->received_date;

        if (in_array($status, ['repaired', 'received', 'unrepairable', 'disposed'])) {
            if (!$receivedDate) {
                $receivedDate = now()->toDateTimeString();
            }
        }

        $repair->update([
            'asset_id'          => $request->asset_id,
            'vendor_id'         => $request->vendor_id,
            'issue_description' => $request->issue_description,
            'reported_date'     => $request->reported_date,
            'sent_date'         => $request->sent_date,
            'received_date'     => $receivedDate,
            'repair_cost'       => $request->repair_cost,
            'repair_status'     => $request->repair_status,
            'remarks'           => $request->remarks,
            'updated_by'        => Auth::id()
        ]);

        // Manage AssetMaster status based on repair status
        if (in_array($status, ['repaired', 'received'])) {
            AssetMaster::where('id', $request->asset_id)->update(['status' => 'available']);
        } elseif ($status === 'unrepairable') {
            AssetMaster::where('id', $request->asset_id)->update(['status' => 'damaged']);
        } elseif ($status === 'disposed') {
            AssetMaster::where('id', $request->asset_id)->update(['status' => 'disposed']);
        } elseif (in_array($status, ['sent for repair', 'under repair', 'reported'])) {
            AssetMaster::where('id', $request->asset_id)->update(['status' => 'under repair']);
        }

        return redirect()
            ->route('asset-repairs.index')
            ->with('success', 'Asset repair updated successfully.');
    }

    /* ================= DESTROY ================= */
    public function destroy(AssetRepair $repair)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        // Revert asset status to available
        AssetMaster::where('id', $repair->asset_id)->update(['status' => 'available']);

        $repair->delete();

        return redirect()
            ->route('asset-repairs.index')
            ->with('success', 'Asset repair record deleted successfully.');
    }

    public function history($assetId)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $repairs = AssetRepair::with('vendor')
            ->where('asset_id', $assetId)
            ->latest()
            ->get();

        return response()->json($repairs);
    }
}