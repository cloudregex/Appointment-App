<?php

namespace App\Http\Controllers;

use App\Services\TenantManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DrugController extends Controller
{

    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * GET /api/drugs
     * Optional filters: ipdNo, toDate, search
     */
    public function index(Request $request)
    {
        $q = DB::connection('tenant')
            ->table('DrugChart')
            ->where('IPDNo', $request->ipdNo);

        if ($request->filled('Date')) {
            $q->whereDate('Date', $request->Date);
        } else {
            $q->whereDate('Date', Carbon::today());
        }

        try {
            $rows = $q->paginate(20);
            return response()->json($rows, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/drugs/{id}
     */
    public function show($id)
    {
        $row = DB::connection('tenant')->table('DrugChart')->where('DurgOID', $id)->first();

        if (!$row) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($row, 200);
    }

    /**
     * POST /api/drugs
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'POID' => 'nullable',
            'Name' => 'nullable|string|max:200',
            'IPDNo' => 'nullable|max:200',
            'Date' => 'required|date',
            'Medicine' => 'nullable|string|max:200',
            'Dosage' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        try {
            $id = DB::connection('tenant')->table('DrugChart')->insertGetId($data);
            return response()->json(['message' => 'Drug record created', 'id' => $id], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create record', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT or PATCH /api/drugs/{id}
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'POID' => 'nullable',
            'Name' => 'nullable|string|max:200',
            'IPDNo' => 'nullable|max:200',
            'Date' => 'nullable|date',
            'Medicine' => 'nullable|string|max:200',
            'Dosage' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = array_filter($validator->validated(), fn($v) => !is_null($v));

        try {
            // ✅ Check record exists
            $exists = DB::connection('tenant')->table('DrugChart')->where('DurgOID', $id)->exists();

            if (!$exists) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            // ✅ Update record
            DB::connection('tenant')->table('DrugChart')->where('DurgOID', $id)->update($data);

            return response()->json([
                'message' => 'Drug record updated successfully',
                'id' => $id,
                'updated_data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * DELETE /api/drugs/{id}
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::connection('tenant')->table('DrugChart')->where('DurgOID', $id)->delete();

            if (!$deleted) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            return response()->json(['message' => 'Drug record deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Delete failed', 'error' => $e->getMessage()], 500);
        }
    }
}
