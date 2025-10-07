<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TenantManager;

class PatientController extends Controller
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
            ->table('pateintreg as p')
            ->leftJoin('drreg as d', 'p.DrOID', '=', 'd.DrOID');

        // Apply search if query parameter is present
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('p.Pcontact', 'LIKE', "%{$search}%")
                    ->orWhere('p.Paddress', 'LIKE', "%{$search}%")
                    ->orWhere('p.Pname', 'LIKE', "%{$search}%")
                    ->orWhere('p.RegNo', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->paginate(20);

        return response()->json($patients);
    }


    /**
     * Store a newly created patient.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'Pname' => 'required|string|max:50',
            'Paddress' => 'nullable|string|max:200',
            'Pcontact' => 'nullable|string|max:50',
            'Pgender' => 'nullable|string|max:50',
            'Page' => 'nullable|string|max:50',
            'DrOID' => 'nullable|integer',
            'Tital' => 'nullable|string|max:50',
            // 'photo' => 'nullable|string',
        ]);

        // Step 1: Static prefix
        $prefix =  $request->HospitalPrefix;

        // Step 2: Current year & month
        $year  = date('Y');
        $month = date('m');

        // Step 3: Find last RegNo (ignoring prefix)
        $lastEntry = DB::connection('tenant')
            ->table('pateintreg')
            ->orderByDesc('POID')
            ->first();

        $nextNumber =  $lastEntry->POID + 1;

        // Step 5: Generate RegNo
        $generatedRegNo = $prefix . '-' . $year . '/' . $month . '/' . $nextNumber;

        // Step 6: Insert patient data
        $patientId = DB::connection('tenant')->table('pateintreg')->insertGetId([
            'Pname'    => $validatedData['Pname'] ?? null,
            'Paddress' => $validatedData['Paddress'] ?? null,
            'Pcontact' => $validatedData['Pcontact'] ?? null,
            'Pgender'  => $validatedData['Pgender'] ?? null,
            'Page'     => $validatedData['Page'] ?? null,
            'DrOID'    => $validatedData['DrOID'] ?? null,
            'RegNo'    => $generatedRegNo,
            'Tital'    => $validatedData['Tital'] ?? null,
            // 'photo'    => $validatedData['photo'] ?? null,
        ]);

        // Step 7: Retrieve the created patient
        $patient = DB::connection('tenant')->table('pateintreg')->where('POID', $patientId)->first();

        // Step 8: Return response
        return response()->json($patient, 201);
    }


    /**
     * Display the specified patient.
     */
    public function show($id)
    {
        $patient = DB::connection('tenant')->table('pateintreg')->where('POID', $id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Update the specified patient.
     */
    public function update(Request $request, $id)
    {
        // Check if patient exists
        $patient = DB::connection('tenant')->table('pateintreg')->where('POID', $id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Validate request data
        $validatedData = $request->validate([
            'Pname' => 'required|string|max:50',
            'Paddress' => 'nullable|string|max:200',
            'Pcontact' => 'nullable|string|max:50',
            'Pgender' => 'nullable|string|max:50',
            'Page' => 'nullable|string|max:50',
            'DrOID' => 'nullable|integer',
            'Tital' => 'nullable|string|max:50',
            // 'photo' => 'nullable|string',
        ]);

        // Update patient data
        DB::connection('tenant')->table('pateintreg')->where('POID', $id)->update([
            'Pname' => $validatedData['Pname'] ?? null,
            'Paddress' => $validatedData['Paddress'] ?? null,
            'Pcontact' => $validatedData['Pcontact'] ?? null,
            'Pgender' => $validatedData['Pgender'] ?? null,
            'Page' => $validatedData['Page'] ?? null,
            'DrOID' => $validatedData['DrOID'] ?? null,
            'Tital' => $validatedData['Tital'] ?? null,
            // 'photo' => $validatedData['photo'] ?? null,
        ]);

        // Retrieve the updated patient
        $updatedPatient = DB::connection('tenant')->table('pateintreg')->where('POID', $id)->first();

        return response()->json($updatedPatient);
    }

    /**
     * Remove the specified patient.
     */
    public function destroy($id)
    {
        // Check if patient exists
        $patient = DB::connection('tenant')->table('pateintreg')->where('POID', $id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Delete the patient
        DB::connection('tenant')->table('pateintreg')->where('POID', $id)->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }

    /**
     * Display a listing of the patients.
     */
    public function doctorsList(Request $request)
    {
        $query = DB::connection('tenant')->table('drreg');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }
        $doctors = $query->limit(10)->get();

        return response()->json($doctors);
    }
    public function PatientList(Request $request)
    {
        $query = DB::connection('tenant')->table('pateintreg');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('Pname', 'LIKE', "%{$search}%");
        }
        $patient = $query->limit(10)->get();

        return response()->json($patient);
    }
    public function ItemList(Request $request)
    {
        $query = DB::connection('tenant')->table('itemreg');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('ItemName', 'LIKE', "%{$search}%");
        }
        $patient = $query->limit(10)->get();

        return response()->json($patient);
    }
    public function ContentList(Request $request)
    {
        $query = DB::connection('tenant')->table('content');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('ContentName', 'LIKE', "%{$search}%");
        }
        $patient = $query->limit(10)->get();

        return response()->json($patient);
    }
}
