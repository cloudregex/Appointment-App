<?php

namespace App\Http\Controllers;

use App\Services\TenantManager;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Display a listing of the patients.
     */
    public function doctorsList()
    {
        $patients = DB::connection('tenant')
            ->table('drreg')
            ->get();

        return response()->json($patients);
    }
}
