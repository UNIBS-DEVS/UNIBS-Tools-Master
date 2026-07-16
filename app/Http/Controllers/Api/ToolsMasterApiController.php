<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ToolsMaster;
use Illuminate\Http\JsonResponse;

class ToolsMasterApiController extends Controller
{
    public function index(): JsonResponse
    {
        $tool = ToolsMaster::first();

        if (!$tool) {
            return response()->json([
                'status' => false,
                'message' => 'Configuration not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Configuration fetched successfully.',
            'data' => $tool,
        ]);
    }
}
