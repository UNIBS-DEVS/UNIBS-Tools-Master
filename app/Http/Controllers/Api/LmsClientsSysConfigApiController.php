<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use App\Models\LmsClientsMaster; 

class LmsClientsSysConfigApiController extends Controller
{
    public function getLmsClientsSysConfigApi($client_code)
    {
        $client = LmsClientsMaster::where('client_code', $client_code)
            ->where('status', 'active')
            ->with('lmsClientsSysConfig')
            ->first();

        if (!$client || !$client->lmsClientsSysConfig) {
            return response()->json([
                'status' => false,
                'data' => (object)[]
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $client->lmsClientsSysConfig
        ], 200);
    }
}
