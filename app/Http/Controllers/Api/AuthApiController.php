<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ToolsMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $config = ToolsMaster::first();

        $authType = $config->authentication_type ?? 'basic';

        $user = null;

        /*
        |--------------------------------------------------------------------------
        | BASIC LOGIN
        |--------------------------------------------------------------------------
        */
        if ($authType === 'basic') {

            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (
                !$user ||
                !Hash::check($request->password, $user->password)
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MICROSOFT LOGIN
        |--------------------------------------------------------------------------
        */ elseif ($authType === 'microsoft') {

            $request->validate([
                'code' => 'required'
            ]);

            $response = Http::asForm()->post(
                "https://login.microsoftonline.com/{$config->tenant_id}/oauth2/v2.0/token",
                [
                    'client_id' => $config->client_id,
                    'client_secret' => $config->client_secret_value,
                    'code' => $request->code,
                    'redirect_uri' => $config->redirect_url,
                    'grant_type' => 'authorization_code',
                ]
            );

            $tokenData = $response->json();

            if (!isset($tokenData['access_token'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Microsoft token failed'
                ], 400);
            }

            $userResponse = Http::withToken(
                $tokenData['access_token']
            )->get('https://graph.microsoft.com/v1.0/me');

            $msUser = $userResponse->json();

            $email = $msUser['mail']
                ?? $msUser['userPrincipalName']
                ?? null;

            if (!$email) {
                return response()->json([
                    'status' => false,
                    'message' => 'Microsoft user fetch failed'
                ], 400);
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $msUser['displayName'] ?? 'Microsoft User',
                    'password' => bcrypt(Str::random(32)),
                ]
            );
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Invalid authentication type'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | USER STATUS CHECK
        |--------------------------------------------------------------------------
        */
        if ($user->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Account is inactive'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE APP SECURITY (OPTIONAL)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('application')) {

            $request->validate([
                'auth_type'   => 'required|in:imei,device_id',
                'application' => 'required|in:attendance,call review',
            ]);

            $mobileType = null;
            $mobile = null;

            if (
                in_array(
                    $request->application,
                    $user->personal_mobile_app ?? []
                )
            ) {
                $mobileType = 'personal';
                $mobile = $user->personal_mobile;
            } elseif (
                in_array(
                    $request->application,
                    $user->offical_mobile_app ?? []
                )
            ) {
                $mobileType = 'official';
                $mobile = $user->offical_mobile;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Application not assigned to this user.'
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | IMEI VALIDATION
            |--------------------------------------------------------------------------
            */
            if ($request->auth_type === 'imei') {

                if ($mobileType === 'personal') {

                    $validImei =
                        $request->mobile_imei_1 == $user->personal_imei_1 &&
                        $request->mobile_imei_2 == $user->personal_imei_2;
                } else {

                    $validImei =
                        $request->mobile_imei_1 == $user->offical_imei_1 ||
                        $request->mobile_imei_2 == $user->offical_imei_2;
                }

                if (!$validImei) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized IMEI.'
                    ], 403);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DEVICE ID VALIDATION
            |--------------------------------------------------------------------------
            */
            if ($request->auth_type === 'device_id') {

                $expectedDeviceId = in_array(
                    $request->application,
                    $user->personal_mobile_app ?? []
                )
                    ? $user->personal_device_id
                    : $user->offical_device_id;

                if (
                    $expectedDeviceId !==
                    $request->mobile_device_id
                ) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized device.'
                    ], 403);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE TOKEN
        |--------------------------------------------------------------------------
        */
        $token = $user->createToken('api-token')->plainTextToken;

        $isAdmin = in_array('admin', $user->roles ?? []);

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $isAdmin,
                'user_type' => $isAdmin ? 'admin' : 'other',
            ],
        ]);
    }
}
