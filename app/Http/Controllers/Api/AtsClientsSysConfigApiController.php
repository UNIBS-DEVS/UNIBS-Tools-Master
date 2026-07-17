<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AtsClientsMaster;

class AtsClientsSysConfigApiController extends Controller
{
    public function getAtsClientsSysConfigApi(string $client_code)
    {
        $client = AtsClientsMaster::where('client_code', $client_code)
            ->where('status', 'active')
            ->with('atsClientsSysConfig')
            ->first();

        if (!$client || !$client->atsClientsSysConfig) {
            return response()->json([
                'status' => false,
                'data' => (object)[]
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $client->atsClientsSysConfig
        ], 200);
    }
}
