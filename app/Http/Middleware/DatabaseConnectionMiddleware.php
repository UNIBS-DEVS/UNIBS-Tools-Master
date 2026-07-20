<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $credentials = $request->session()->get('db_credentials');

        if (!$credentials) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Database connection required.'], 401);
            }
            return redirect()->route('db-settings');
        }

        // Dynamically configure the 'dynamic' connection
        $connectionConfig = [
            'driver' => $credentials['driver'],
            'prefix' => '',
        ];

        if ($credentials['driver'] === 'sqlite') {
            // If it is sqlite, the database name is the path to the sqlite file
            $connectionConfig['database'] = $credentials['database'];
            $connectionConfig['foreign_key_constraints'] = true;
        } else {
            $connectionConfig['host'] = $credentials['host'] ?? '127.0.0.1';
            $connectionConfig['port'] = $credentials['port'] ?? '3306';
            $connectionConfig['database'] = $credentials['database'];
            $connectionConfig['username'] = $credentials['username'] ?? 'root';
            $connectionConfig['password'] = $credentials['password'] ?? '';
            
            if ($credentials['driver'] === 'mysql') {
                $connectionConfig['charset'] = 'utf8mb4';
                $connectionConfig['collation'] = 'utf8mb4_unicode_ci';
                $connectionConfig['strict'] = true;
                $connectionConfig['engine'] = null;
            }
        }

        config(['database.connections.dynamic' => $connectionConfig]);

        return $next($request);
    }
}
