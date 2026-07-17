<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AtsClientsMaster;
use App\Models\LmsClientsMaster;
use App\Models\UnioneClientsMaster;

class GetAppTenantApiController extends Controller
{
    public function getAppTenantsApi($app_name)
    {
        $appName = strtolower($app_name);

        $applications = [
            'ats' => AtsClientsMaster::class,
            'lms' => LmsClientsMaster::class,
            'unione' => UnioneClientsMaster::class,
        ];

        if (!isset($applications[$appName])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid application.'
            ], 400);
        }

        $clientsModel = $applications[$appName];

        $clients = $clientsModel::all();

        if (!$clients) {
            return response()->json([
                'status' => false,
                'message' => 'Clients not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Tenant Clients fetched successfully.',
            'data' => $clients
        ]);
    }
}
