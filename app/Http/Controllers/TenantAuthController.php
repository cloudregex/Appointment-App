<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'db_host'     => 'required|string',
            'db_port'     => 'required|string',
            'db_name'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'required|string',
        ]);

        $credentials = $request->only([
            'db_host',
            'db_port',
            'db_name',
            'db_username',
            'db_password'
        ]);

        // 1️⃣ Try to connect using given credentials
        Config::set('database.connections.test_tenant', [
            'driver'   => 'mysql',
            'host'     => $credentials['db_host'],
            'port'     => $credentials['db_port'],
            'database' => $credentials['db_name'],
            'username' => $credentials['db_username'],
            'password' => $credentials['db_password'],
            'charset'  => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        try {
            DB::connection('test_tenant')->getPdo(); // 👈 connection test
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid database credentials'], 401);
        }

        // 2️⃣ Check if tenant already exists
        $tenant = Tenant::where($credentials)->first();

        if (!$tenant) {
            $tenant = Tenant::create($credentials);
        }

        // 3️⃣ Generate token with tenant_id
        $payload = [
            'tenant_id' => $tenant->id,
            'db_name'   => $tenant->db_name,
            'iat'       => time(),
        ];

        $token = base64_encode(json_encode($payload));

        return response()->json([
            'token'   => $token,
            'tenant'  => $tenant,
            'message' => 'Login successful',
        ]);
    }
}
