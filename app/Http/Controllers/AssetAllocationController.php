<?php

namespace App\Http\Controllers;

use App\Models\AssetAllocation;
use App\Models\AssetMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetAllocationController extends Controller
{
    /* ================= LIST ================= */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        return redirect()->route('asset.index');
    }

    /* ================= CREATE FORM ================= */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $assetsQuery = AssetMaster::query();
        
        if (request('asset_id')) {
            $assetsQuery->where('status', 'available')->orWhere('id', request('asset_id'));
        } else {
            $assetsQuery->where('status', 'available');
        }
        $assets = $assetsQuery->orderBy('asset_name')->get();

        $employees = User::orderBy('name')->get();

        return view('asset_allocations.create', compact('assets', 'employees'));
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'asset_id'       => 'required|exists:asset_masters,id',
            'employee_id'    => 'required|exists:users,id',
            'allocated_date' => 'required|date',
            'status'         => 'required|string|max:50',
            'remarks'        => 'nullable|string|max:500'
        ]);

        AssetAllocation::create([
            'asset_id'       => $request->asset_id,
            'employee_id'    => $request->employee_id,
            'allocated_date' => $request->allocated_date,
            'status'         => $request->status,
            'remarks'        => $request->remarks,
            'end_date'       => $request->end_date , 
            'created_by'     => Auth::id(),
            'updated_by'     => Auth::id()
        ]);

        // Update asset master status
        AssetMaster::where('id', $request->asset_id)->update([
            'status' => 'allocated'
        ]);

        return redirect()
            ->route('asset-allocations.index')
            ->with('success', 'Asset allocated successfully.');
    }

    /* ================= SHOW ================= */
    public function show(AssetAllocation $allocation)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $allocation->load(['asset', 'employee']);

        return view('asset_allocations.show', compact('allocation'));
    }

    /* ================= EDIT FORM ================= */
    public function edit(AssetAllocation $allocation)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $employees = User::orderBy('name')->get();
        
        // Include the currently allocated asset in the assets dropdown so it shows up
        $assets = AssetMaster::where('status', 'available')
            ->orWhere('id', $allocation->asset_id)
            ->orderBy('asset_name')
            ->get();

        return view('asset_allocations.edit', compact('allocation', 'assets', 'employees'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, AssetAllocation $allocation)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'asset_id'       => 'required|exists:asset_masters,id',
            'employee_id'    => 'required|exists:users,id',
            'allocated_date' => 'required|date',
            'returned_date'  => 'nullable|date',
            'status'         => 'required|string|max:50',
            'remarks'        => 'nullable|string|max:500'
        ]);

        $oldAssetId = $allocation->asset_id;
        $newAssetId = $request->asset_id;

        // If returned_date is provided or status is Returned/Available/Damaged etc., handle asset master status
        $returnedDate = $request->returned_date;
        $status = $request->status;

        if (in_array(strtolower($status), ['returned', 'available', 'damaged', 'lost', 'disposed'])) {
            if (!$returnedDate) {
                $returnedDate = now()->toDateTimeString();
            }
        }

        $allocation->update([
            'asset_id'       => $newAssetId,
            'employee_id'    => $request->employee_id,
            'allocated_date' => $request->allocated_date,
            'returned_date'  => $returnedDate,
            'status'         => $status,
            'remarks'        => $request->remarks,
            'updated_by'     => Auth::id()
        ]);

        // If asset changed, free the old asset and allocate the new one
        if ($oldAssetId != $newAssetId) {
            AssetMaster::where('id', $oldAssetId)->update(['status' => 'available']);
            AssetMaster::where('id', $newAssetId)->update(['status' => 'allocated']);
        } else {
            // If the asset is the same but status changed to returned/available/damaged/etc.
            if (in_array(strtolower($status), ['returned', 'available', 'damaged', 'lost', 'disposed'])) {
                // If it is damaged or lost, update asset status accordingly, else set to available
                $assetStatus = in_array(strtolower($status), ['damaged', 'lost', 'disposed']) ? strtolower($status) : 'available';
                AssetMaster::where('id', $newAssetId)->update(['status' => $assetStatus]);
            } else {
                // Otherwise keep it allocated
                AssetMaster::where('id', $newAssetId)->update(['status' => 'allocated']);
            }
        }

        return redirect()
            ->route('asset-allocations.index')
            ->with('success', 'Asset allocation updated successfully.');
    }

    /* ================= DESTROY ================= */
    public function destroy(AssetAllocation $allocation)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        // Revert asset status back to available
        AssetMaster::where('id', $allocation->asset_id)->update([
            'status' => 'available'
        ]);

        $allocation->delete();

        return redirect()
            ->route('asset-allocations.index')
            ->with('success', 'Asset allocation record deleted successfully.');
    }
}