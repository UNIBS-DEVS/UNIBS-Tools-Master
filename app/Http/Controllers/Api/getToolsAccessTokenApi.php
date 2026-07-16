<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AtsClientMaster;
use App\Models\AtsClientsSysConfig;
use App\Models\LmsClientMaster;
use App\Models\LmsClientsSysConfig;
use App\Models\UnioneClientsMaster;
use App\Models\UnioneClientsSysConfig;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class getToolsAccessTokenApi extends Controller
{
    public function getToolsAccessTokenApi(Request $request)
    {
        $request->validate([
            'client_code' => 'required|string',
            'email'       => 'required|email',
            'password'    => 'required|string',
            'app_name'    => 'required|string',
        ]);

        // Authenticate User
        $user = User::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        }

        // Check API Role
        if (!$user->hasRole('api user')) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authorized.'
            ], 403);
        }

        $appName = strtolower($request->app_name);

        /**
         * Register all applications here.
         * Just add a new entry when a new application is created.
         */
        $applications = [
            'ats' => [
                'client_model' => AtsClientMaster::class,
                'config_model' => AtsClientsSysConfig::class,
                'api_url'      => env('ATS_API_URL'),
            ],

            'lms' => [
                'client_model' => LmsClientMaster::class,
                'config_model' => LmsClientsSysConfig::class,
                'api_url'      => env('LMS_API_URL'),
            ],

            'unione' => [
                'client_model' => UnioneClientsMaster::class,
                'config_model' => UnioneClientsSysConfig::class,
                'api_url'      => env('UNIONE_API_URL'),
            ],
        ];

        if (!isset($applications[$appName])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid application.'
            ], 400);
        }

        $clientModel = $applications[$appName]['client_model'];
        $configModel = $applications[$appName]['config_model'];

        // Get Client
        $client = $clientModel::where('client_code', $request->client_code)
            ->where('status', 'active')
            ->first();

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid client code.'
            ], 404);
        }

        // Get Client System Configuration
        $config = $configModel::where('client_id', $client->id)->first();

        if (!$config) {
            return response()->json([
                'status' => false,
                'message' => 'Client configuration not found.'
            ], 404);
        }

        // Create Sanctum Token
        $token = $user->createToken(
            "{$appName}-access-token",
            [$appName]
        )->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Access token generated successfully.',

            'token' => $token,
        ]);
    }
}
