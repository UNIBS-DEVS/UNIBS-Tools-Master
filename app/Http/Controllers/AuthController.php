<?php

namespace App\Http\Controllers;

use App\Models\ToolsMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ✅ LOGIN PAGE
    public function login()
    {
        $config = ToolsMaster::first(); 

        return view('auth.login', [
            'authType' => $config->login_auth_type ?? 'basic',
        ]);
    }

    // ✅ BASIC LOGIN
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($request->only('email', 'password'), $remember)) {

            $request->session()->regenerate();

            // ✅ store login type
            session(['login_type' => 'basic']);

            return redirect()->route('dashboard.index');
        }

        return back()->with('error', 'Invalid email or password');
    }


    // ✅ REDIRECT TO MICROSOFT
    public function redirectToMicrosoft()
    {

        $config = ToolsMaster::first();
        // dd($config);

        $url = "https://login.microsoftonline.com/" . $config->graph_tenant_id . "/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $config->graph_client_id,
            'response_type' => 'code',
            'redirect_uri' => $config->graph_redirect_url,
            'response_mode' => 'query',
            'scope' => 'User.Read offline_access',
        ]);

        return redirect($url);
    }

    // ✅ MICROSOFT CALLBACK (FIXED PROPERLY)
    public function handleMicrosoftCallback(Request $request)
    {
        $code = $request->code;

        if (!$code) {
            return redirect()->route('login')->with('error', 'Login failed');
        }

        $config = ToolsMaster::first();

        // STEP 1: GET ACCESS TOKEN
        $response = Http::asForm()->post(
            "https://login.microsoftonline.com/" . $config->graph_tenant_id . "/oauth2/v2.0/token",
            [
                'client_id' => $config->graph_client_id,
                'client_secret' => $config->graph_client_secret_value,
                'code' => $code,
                'redirect_uri' => $config->graph_redirect_url,
                'grant_type' => 'authorization_code',
            ]
        );

        $token = $response->json();

        // dd($token);

        if (!isset($token['access_token'])) {
            return redirect()->route('login')->with('error', 'Token failed');
        }

        $accessToken = $token['access_token'];

        // 🔹 STEP 2: GET USER INFO
        $userResponse = Http::withToken($accessToken)
            ->get('https://graph.microsoft.com/v1.0/me');

        $msUser = $userResponse->json();

        if (!isset($msUser['mail']) && !isset($msUser['userPrincipalName'])) {
            return redirect()->route('login')->with('error', 'User fetch failed');
        }

        $email = $msUser['mail'] ?? $msUser['userPrincipalName'];

        // 🔹 STEP 3: CREATE OR LOGIN USER
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $msUser['displayName'] ?? 'Microsoft User',
                'password' => bcrypt(Str::random(16))
            ]
        );

        Auth::login($user);

        $loginType = $config->login_auth_type ?? 'basic';

        // ✅ store login type
        session(['login_type' => $loginType]);
 

        $token = $user->createToken('api-token')->plainTextToken;

        return redirect()->route('dashboard.index');
    }

    // ✅ LOGOUT (FIXED)
    public function logout(Request $request)
    {
        $loginType = session('login_type'); // 👈 important

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Only logout from Microsoft if OAuth
        if ($loginType === 'oauth') {

            $config = ToolsMaster::first();

            $logoutUrl = "https://login.microsoftonline.com/" . $config->graph_tenant_id . "/oauth2/v2.0/logout?" . http_build_query([
                'post_logout_redirect_uri' => route('login')
            ]);

            return redirect($logoutUrl);
        }

        // ✅ normal logout
        return redirect()->route('login');
    }
}
