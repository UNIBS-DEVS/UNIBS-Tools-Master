<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequirementType;
use Illuminate\Http\Request;

class RequirementTypeApiController extends Controller
{
    public function requirementTypes(Request $request)
    {
        $request->validate([
            'app_id' => 'required|integer|exists:applications,id',
            'client_id' => 'required|integer',
        ]);

        $requirementTypes = RequirementType::where('status', 1)
            ->where('app_id', $request->app_id)
            ->where('client_id', $request->client_id)
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        // dd($requirementTypes);

        return response()->json([
            'status' => true,
            'message' => 'Requirement types fetched successfully.',
            'data' => $requirementTypes,
        ], 200);
    }
}
