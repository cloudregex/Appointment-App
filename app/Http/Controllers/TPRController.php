<?php

namespace App\Http\Controllers;

use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TPRController extends Controller
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * List TPR rows with filters. Example:
     * GET /api/tpr?ipdNo=918&fromDate=2025-10-02&toDate=2025-10-03&search=120/80&per_page=20
     */
    public function index(Request $request)
    {
        // Make sure 'tenant' connection exists in config/database.php
        $q = DB::connection('tenant')->table('TPR as t')
            ->when($request->ipdNo, function ($query) use ($request) {
                $query->where('t.IPDNo', $request->ipdNo);
            });

        // Optional search filter
        if ($search = $request->get('search')) {
            $q->where(function ($w) use ($search) {
                $w->where('t.Name', 'LIKE', "%{$search}%")
                    ->orWhere('t.IPDNo', 'LIKE', "%{$search}%")
                    ->orWhere('t.bp', 'LIKE', "%{$search}%")
                    ->orWhere('t.a', 'LIKE', "%{$search}%");
            });
        }

        try {
            $rows = $q->latest('Time')->paginate(20);
            return response()->json($rows, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch TPR rows',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single record
     * GET /api/tpr/{id}
     */
    public function show($id)
    {
        try {
            $row = DB::connection('tenant')
                ->table('TPR')
                ->where('TPROID', $id)
                ->first();

            if (!$row) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            return response()->json($row, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new TPR record
     * POST /api/tpr
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'  => 'required|date_format:Y-m-d',
            'time'  => 'nullable',
            'poid'  => 'nullable|integer',
            'name'  => 'nullable|max:100',
            'ipdNo' => 'required|max:50',

            't'  => 'nullable|max:10',
            'p'  => 'nullable|max:10',
            'r'  => 'nullable|max:10',
            'bp' => 'nullable|max:20',
            'it' => 'nullable|max:100',
            'op' => 'nullable|max:100',
            'c'  => 'nullable|max:100',
            'a'  => 'nullable|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'Date'  => $request->input('date'),
            'Time'  => $request->input('time'),
            'POID'  => $request->input('poid'),
            'Name'  => $request->input('name'),
            'IPDNo' => $request->input('ipdNo'),
            'T'     => $request->input('t'),
            'P'     => $request->input('p'),
            'R'     => $request->input('r'),
            'bp'    => $request->input('bp'),
            'it'    => $request->input('it'),
            'op'    => $request->input('op'),
            'c'     => $request->input('c'),
            'a'     => $request->input('a'),
        ];

        try {
            $id = DB::connection('tenant')->table('TPR')->insertGetId($data);

            return response()->json([
                'message' => 'TPR record created',
                'id' => $id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create TPR record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update TPR record
     * PUT /api/tpr/{id}
     * PATCH /api/tpr/{id}
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date'  => 'required|date_format:Y-m-d',
            'time'  => 'nullable',
            'poid'  => 'nullable|integer',
            'name'  => 'nullable|max:100',
            'ipdNo' => 'required|max:50',

            't'  => 'nullable|max:10',
            'p'  => 'nullable|max:10',
            'r'  => 'nullable|max:10',
            'bp' => 'nullable|max:20',
            'it' => 'nullable|max:100',
            'op' => 'nullable|max:100',
            'c'  => 'nullable|max:100',
            'a'  => 'nullable|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Map only provided fields
        $map = [
            'date'  => 'Date',
            'time'  => 'Time',
            'poid'  => 'POID',
            'name'  => 'Name',
            'ipdNo' => 'IPDNo',
            't' => 'T',
            'p' => 'P',
            'r' => 'R',
            'bp' => 'bp',
            'it' => 'it',
            'op' => 'op',
            'c' => 'c',
            'a' => 'a',
        ];

        $update = [];
        foreach ($map as $reqKey => $col) {
            if ($request->has($reqKey)) {
                $update[$col] = $request->input($reqKey);
            }
        }

        if (empty($update)) {
            return response()->json([
                'message' => 'No updatable fields provided'
            ], 400);
        }

        try {
            $affected = DB::connection('tenant')
                ->table('TPR')
                ->where('TPROID', $id)
                ->update($update);

            if ($affected === 0) {
                // Could be record not found or no changes
                $exists = DB::connection('tenant')
                    ->table('TPR')
                    ->where('TPROID', $id)
                    ->exists();

                if (!$exists) {
                    return response()->json(['message' => 'Record not found'], 404);
                }
            }

            return response()->json(['message' => 'TPR record updated', 'id' => $id], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete TPR record
     * DELETE /api/tpr/{id}
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::connection('tenant')
                ->table('TPR')
                ->where('TPROID', $id)
                ->delete();

            if (!$deleted) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            return response()->json(['message' => 'TPR record deleted'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
