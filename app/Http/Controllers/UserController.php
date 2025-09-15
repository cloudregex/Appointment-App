<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\TenantManager;

class UserController extends Controller
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }


    public function index()
    {
        $users = DB::connection('tenant')->table('users')->get();

        return response()->json($users);
    }
}
