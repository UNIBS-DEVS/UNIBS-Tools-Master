<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DatabaseInspectorService;

class DatabaseInspectorController extends Controller
{
    public function showSettings()
    {
        return view('db_inspectors.settings');
    }

    public function getClientsApiProxy($appName)
    {
        $api = new \App\Http\Controllers\Api\GetAppTenantApiController();
        return $api->getAppTenantsApi($appName);
    }

    public function connect(Request $request)
    {
        // Immediately forget any existing connection credentials
        $request->session()->forget('db_credentials');

        $request->validate([
            'app_name'   => 'required|in:ats,lms,unione',
            'profile_id' => 'required|string',
        ]);

        $appName = $request->input('app_name');
        $clientCode = $request->input('profile_id');
        $driver = 'mysql';
        $host = '127.0.0.1';
        $port = '3306';
        $username = 'root';
        $password = '';
        $profileDbName = '';

        try {
            if ($appName === 'ats') {
                $api = new \App\Http\Controllers\Api\AtsClientsSysConfigApiController();
                $response = $api->getAtsClientsSysConfigApi($clientCode);
            } elseif ($appName === 'lms') {
                $api = new \App\Http\Controllers\Api\LmsClientsSysConfigApiController();
                $response = $api->getLmsClientsSysConfigApi($clientCode);
            } elseif ($appName === 'unione') {
                $api = new \App\Http\Controllers\Api\UnioneClientsSysConfigApiController();
                $response = $api->getUnioneClientsSysConfigApi($clientCode);
            } else {
                throw new \Exception("Invalid application selected.");
            }

            if ($response->getStatusCode() !== 200) {
                throw new \Exception("Selected client database configuration does not exist or API request failed.");
            }

            $responseArray = json_decode($response->getContent(), true);
            $config = $responseArray['data'] ?? null;

            if (!$config) {
                throw new \Exception("Database configuration details are empty.");
            }

            $driver = 'mysql';
            $host = $config['db_host'] ?? '127.0.0.1';
            $port = $config['db_mysql_port'] ?? '3306';
            $username = $config['db_username'] ?? 'root';
            $password = $config['db_password'] ?? '';
            $profileDbName = trim($config['db_name'] ?? '');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['connection' => $e->getMessage()]);
        }

        // Parse host and port if host contains colon
        if ($host && str_contains($host, ':')) {
            $parts = explode(':', $host);
            $host = $parts[0];
            $port = $port ?: $parts[1];
        }

        $database = null;

        // Discover and match database name
        try {
            if ($driver === 'sqlite') {
                $database = config('database.connections.mysql.database') ?? 'unibs_tools';
            } else {
                // Connect to server (without selecting a specific db where possible)
                $serverDsn = "{$driver}:host={$host}";
                if ($driver === 'pgsql') {
                    $serverDsn .= ";dbname=postgres";
                }
                if ($port) {
                    $serverDsn .= ";port={$port}";
                }

                $serverPdo = new \PDO($serverDsn, $username, $password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => 3,
                ]);

                // Query databases list
                if ($driver === 'pgsql') {
                    $stmt = $serverPdo->query("SELECT datname FROM pg_database WHERE datistemplate = false");
                } elseif ($driver === 'sqlsrv') {
                    $stmt = $serverPdo->query("SELECT name FROM sys.databases");
                } else {
                    $stmt = $serverPdo->query("SHOW DATABASES");
                }
                $databases = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                // 1. Exact case-insensitive match
                foreach ($databases as $db) {
                    if (strtolower($db) === strtolower($profileDbName)) {
                        $database = $db;
                        break;
                    }
                }

                // 2. Fuzzy match
                if (!$database) {
                    foreach ($databases as $db) {
                        if (str_contains(strtolower($db), strtolower($profileDbName))) {
                            $database = $db;
                            break;
                        }
                    }
                }

                // If the configured database name does not exist, throw an exception
                if (!$database) {
                    throw new \Exception("Database '" . $profileDbName . "' does not exist on the database server.");
                }
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['connection' => 'Database discovery failed: ' . $e->getMessage()]);
        }

        // Test connection to selected database
        try {
            if ($driver === 'sqlite') {
                if (!file_exists($database) && file_exists(base_path($database))) {
                    $database = base_path($database);
                } elseif (!file_exists($database)) {
                    throw new \Exception("SQLite database file not found at: " . $database);
                }
                new \PDO("sqlite:" . $database);
            } else {
                $finalDsn = "{$driver}:host={$host};dbname={$database}";
                if ($port) {
                    $finalDsn .= ";port={$port}";
                }

                new \PDO($finalDsn, $username, $password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => 3,
                ]);
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['connection' => 'Connection failed to matched database "' . $database . '": ' . $e->getMessage()]);
        }

        // Store credentials in session
        $request->session()->put('db_credentials', [
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ]);

        return redirect()->route('inspect-database');
    }

    public function disconnect(Request $request)
    {
        $request->session()->forget('db_credentials');
        return redirect()->route('db-settings');
    }

    public function inspect(Request $request, DatabaseInspectorService $inspector)
    {
        $tables = $inspector->getTables();
        $selectedTable = $request->query('table');

        // Default to first table if none selected
        if (!$selectedTable && !empty($tables)) {
            $selectedTable = $tables[0]['name'];
        }

        $schema = [];
        $data = null;
        $relations = ['outgoing' => [], 'incoming' => []];
        $error = null;

        if ($selectedTable) {
            try {
                $schema = $inspector->getTableSchema($selectedTable);
                $relations = $inspector->getTableRelations($selectedTable);
                $search = $request->query('search');
                $perPage = intval($request->query('per_page', 15));
                $data = $inspector->getTableData($selectedTable, $search, $perPage);
            } catch (\Exception $e) {
                $error = $e->getMessage();
                // Return an empty paginator to avoid blade exceptions
                $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'table' => $selectedTable,
                'schema' => $schema,
                'data' => $data,
                'relations' => $relations,
                'error' => $error,
            ]);
        }

        return view('db_inspectors.index', [
            'tables' => $tables,
            'selectedTable' => $selectedTable,
            'schema' => $schema,
            'data' => $data,
            'relations' => $relations,
            'search' => $request->query('search'),
            'error' => $error,
        ]);
    }
}
