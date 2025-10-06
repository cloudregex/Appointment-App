<?php

namespace App\Http\Controllers;

use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrentIPD extends Controller
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Display a listing of the patients.
     */
    public function index(Request $request)
    {
        $query = DB::connection('tenant')
            ->table('currentipd as p');

        // Apply search if query parameter is present
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('p.Name', 'LIKE', "%{$search}%")
                    ->orWhere('p.Room', 'LIKE', "%{$search}%")
                    ->orWhere('p.IPDNO', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->paginate(20);

        return response()->json($patients);
    }
}