<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AssetMaster;
use App\Models\SIMRecharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SIMRechargeController extends Controller
{
    /* ================= INDEX ================= */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $recharges = SIMRecharge::with('asset')
            ->latest('recharge_date')
            ->paginate(10);

        return view('sim_recharges.index', compact('recharges'));
    }

    /* ================= CREATE ================= */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $assets = AssetMaster::where('asset_category', 'like', '%sim%')
            ->orderBy('asset_name')
            ->get();

        // If a specific asset is requested but not in the SIM category query, load it specifically
        if (request('asset_id') && !$assets->contains('id', request('asset_id'))) {
            $specificAsset = AssetMaster::find(request('asset_id'));
            if ($specificAsset) {
                $assets->push($specificAsset);
            }
        }

        return view('sim_recharges.create', compact('assets'));
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'asset_id'        => 'required|exists:asset_masters,id',
            'recharge_date'   => 'required|date',
            'plan_name'       => 'nullable|string|max:100',
            'recharge_amount' => 'nullable|numeric|min:0',
            'validity_days'   => 'nullable|integer|min:1',
            'expiry_date'     => 'nullable|date|after_or_equal:recharge_date',
            'remarks'         => 'nullable|string|max:500'
        ]);

        SIMRecharge::create([
            'asset_id'        => $request->asset_id,
            'recharge_date'   => $request->recharge_date,
            'plan_name'       => $request->plan_name,
            'recharge_amount' => $request->recharge_amount,
            'validity_days'   => $request->validity_days,
            'expiry_date'     => $request->expiry_date,
            'remarks'         => $request->remarks,
            'created_by'      => Auth::id(),
            'updated_by'      => Auth::id()
        ]);

        return redirect()
            ->route('sim-recharges.index')
            ->with('success', 'SIM Recharge logged successfully.');
    }

    /* ================= EDIT ================= */
    public function edit(string $id)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $recharge = SIMRecharge::findOrFail($id);
        
        $assets = AssetMaster::where('asset_category', 'like', '%sim%')
            ->orWhere('id', $recharge->asset_id)
            ->orderBy('asset_name')
            ->get();

        return view('sim_recharges.edit', compact('recharge', 'assets'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, string $id)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $recharge = SIMRecharge::findOrFail($id);

        $request->validate([
            'asset_id'        => 'required|exists:asset_masters,id',
            'recharge_date'   => 'required|date',
            'plan_name'       => 'nullable|string|max:100',
            'recharge_amount' => 'nullable|numeric|min:0',
            'validity_days'   => 'nullable|integer|min:1',
            'expiry_date'     => 'nullable|date|after_or_equal:recharge_date',
            'remarks'         => 'nullable|string|max:500'
        ]);

        $recharge->update([
            'asset_id'        => $request->asset_id,
            'recharge_date'   => $request->recharge_date,
            'plan_name'       => $request->plan_name,
            'recharge_amount' => $request->recharge_amount,
            'validity_days'   => $request->validity_days,
            'expiry_date'     => $request->expiry_date,
            'remarks'         => $request->remarks,
            'updated_by'      => Auth::id()
        ]);

        return redirect()
            ->route('sim-recharges.index')
            ->with('success', 'SIM Recharge updated successfully.');
    }

    /* ================= DESTROY ================= */
    public function destroy(string $id)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts'])) {
            abort(403, 'Unauthorized access');
        }

        $recharge = SIMRecharge::findOrFail($id);
        $recharge->delete();

        return redirect()
            ->route('sim-recharges.index')
            ->with('success', 'SIM Recharge record deleted successfully.');
    }
}