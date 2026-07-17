<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GetToolsAccessTokenApiController extends Controller
{
    public function getToolsAccessTokenApi(Request $request)
    {

        $test = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
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

        // Create Sanctum Token
        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Access token generated successfully.',

            'token' => $token,
        ]);
    }
}
