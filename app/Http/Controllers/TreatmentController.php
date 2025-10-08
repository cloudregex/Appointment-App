<?php

namespace App\Http\Controllers;

use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TreatmentController extends Controller
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    // List Treatment rows with optional search and pagination
    public function index(Request $request)
    {
        $q = DB::connection('tenant')->table('Treatment as t')->where('t.IPDNo', $search = $request->get('IPDNo'));

        if ($search = $request->get('search')) {
            $q->where(function ($w) use ($search) {
                $w->where('t.Name', 'LIKE', "%{$search}%")
                    ->orWhere('t.DrName', 'LIKE', "%{$search}%")
                    ->orWhere('t.ClinicalNote', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $perPage = $perPage > 0 ? $perPage : 20;

        try {
            $rows = $q->paginate($perPage);
            return response()->json($rows, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch Treatment rows', 'error' => $e->getMessage()], 500);
        }
    }

    // Show single Treatment record
    public function show($id)
    {
        try {
            $row = DB::connection('tenant')->table('Treatment')->where('TCOID', $id)->first();
            if (!$row) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            return response()->json($row, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch record', 'error' => $e->getMessage()], 500);
        }
    }

    // Create new Treatment record
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|string|max:20',
            'poid' => 'nullable|integer',
            'IPDNo' => 'nullable|integer',
            'name' => 'nullable|string|max:100',
            'drName' => 'nullable|string|max:100',
            'clinicalNote' => 'nullable|string',
            'advice' => 'nullable|string',
            'rs' => 'nullable|string',
            'cns' => 'nullable|string',
            'cvs' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = [
            'Date' => $request->input('date'),
            'Time' => $request->input('time'),
            'POID' => $request->input('poid'),
            'IPDNo' => $request->input('IPDNo'),
            'Name' => $request->input('name'),
            'DrName' => $request->input('drName'),
            'ClinicalNote' => $request->input('clinicalNote'),
            'Advice' => $request->input('advice'),
            'Rs' => $request->input('rs'),
            'Cns' => $request->input('cns'),
            'Cvs' => $request->input('cvs'),
        ];

        try {
            $id = DB::connection('tenant')->table('Treatment')->insertGetId($data);
            return response()->json(['message' => 'Treatment record created', 'id' => $id], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create record', 'error' => $e->getMessage()], 500);
        }
    }

    // Update Treatment record
    public function update(Request $request, $id)
    {
        $map = [
            'date' => 'Date',
            'time' => 'Time',
            'poid' => 'POID',
            'IPDNo' => 'IPDNo',
            'name' => 'Name',
            'drName' => 'DrName',
            'clinicalNote' => 'ClinicalNote',
            'advice' => 'Advice',
            'rs' => 'Rs',
            'cns' => 'Cns',
            'cvs' => 'Cvs'
        ];

        $update = [];
        foreach ($map as $k => $col) {
            if ($request->has($k)) {
                $update[$col] = $request->input($k);
            }
        }

        if (empty($update)) {
            return response()->json(['message' => 'No updatable fields provided'], 400);
        }

        try {
            $affected = DB::connection('tenant')->table('Treatment')->where('TCOID', $id)->update($update);
            if ($affected === 0) {
                $exists = DB::connection('tenant')->table('Treatment')->where('TCOID', $id)->exists();
                if (!$exists) {
                    return response()->json(['message' => 'Record not found'], 404);
                }
                return response()->json(['message' => 'No changes made'], 200);
            }
            return response()->json(['message' => 'Treatment record updated', 'id' => $id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update record', 'error' => $e->getMessage()], 500);
        }
    }

    // Delete Treatment record
    public function destroy($id)
    {
        try {
            $deleted = DB::connection('tenant')->table('Treatment')->where('TCOID', $id)->delete();
            if (!$deleted) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            return response()->json(['message' => 'Treatment record deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete record', 'error' => $e->getMessage()], 500);
        }
    }
}
