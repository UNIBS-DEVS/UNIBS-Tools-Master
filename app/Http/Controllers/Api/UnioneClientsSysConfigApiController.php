<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnioneClientsMaster;

class UnioneClientsSysConfigApiController extends Controller
{
    public function getUnioneClientsSysConfigApi(string $client_code)
    {
        $client = UnioneClientsMaster::where('client_code', $client_code)
            ->where('status', 'active')
            ->with('unioneClientsSysConfig')
            ->first();

        if (!$client || !$client->unioneClientsSysConfig) {
            return response()->json([
                'status' => false,
                'data' => (object)[]
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $client->unioneClientsSysConfig
        ], 200);
    }
}
