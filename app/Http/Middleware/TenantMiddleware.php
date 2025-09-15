<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract tenant ID from the token instead of header
        $token = $request->header('Authorization');
        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return response()->json(['error' => 'Authorization token missing'], 401);
        }

        $token = substr($token, 7); // Remove 'Bearer ' prefix

        $payload = json_decode(base64_decode($token), true);

        if (!$payload || !isset($payload['tenant_id'])) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $tenantId = $payload['tenant_id'];
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $this->tenantManager->setTenant($tenant);

        return $next($request);
    }
}
