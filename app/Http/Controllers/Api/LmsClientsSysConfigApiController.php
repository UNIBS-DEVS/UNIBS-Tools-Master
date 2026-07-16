<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LmsClientMaster;
use Illuminate\Http\Request;

class LmsClientsSysConfigApiController extends Controller
{
    public function getLmsSysConfig($client_code)
    {
        $client = LmsClientMaster::where('client_code', $client_code)
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
