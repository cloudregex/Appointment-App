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
    public function index()
    {
        $patients = DB::connection('tenant')
            ->table('pateintreg')
            ->paginate(20);

        return response()->json($patients);
    }

    /**
     * Store a newly created patient.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'RegNo' => 'nullable|string|max:50',
            'Pname' => 'nullable|string|max:50',
            'Paddress' => 'nullable|string|max:200',
            'Pcontact' => 'nullable|string|max:50',
            'Pgender' => 'nullable|string|max:50',
            'Page' => 'nullable|string|max:50',
            'DrOID' => 'nullable|integer',
            'Tital' => 'nullable|string|max:50',
            'photo' => 'nullable|string',
        ]);

        // Insert patient data
        $patientId = DB::connection('tenant')->table('pateintreg')->insertGetId([
            'RegNo' => $validatedData['RegNo'] ?? null,
            'Pname' => $validatedData['Pname'] ?? null,
            'Paddress' => $validatedData['Paddress'] ?? null,
            'Pcontact' => $validatedData['Pcontact'] ?? null,
            'Pgender' => $validatedData['Pgender'] ?? null,
            'Page' => $validatedData['Page'] ?? null,
            'DrOID' => $validatedData['DrOID'] ?? null,
            'Tital' => $validatedData['Tital'] ?? null,
            'photo' => $validatedData['photo'] ?? null,
        ]);

        // Retrieve the created patient
        $patient = DB::connection('tenant')->table('pateintreg')->where('POID', $patientId)->first();

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
            'RegNo' => 'nullable|string|max:50',
            'Pname' => 'nullable|string|max:50',
            'Paddress' => 'nullable|string|max:200',
            'Pcontact' => 'nullable|string|max:50',
            'Pgender' => 'nullable|string|max:50',
            'Page' => 'nullable|string|max:50',
            'DrOID' => 'nullable|integer',
            'Tital' => 'nullable|string|max:50',
            'photo' => 'nullable|string',
        ]);

        // Update patient data
        DB::connection('tenant')->table('pateintreg')->where('POID', $id)->update([
            'RegNo' => $validatedData['RegNo'] ?? null,
            'Pname' => $validatedData['Pname'] ?? null,
            'Paddress' => $validatedData['Paddress'] ?? null,
            'Pcontact' => $validatedData['Pcontact'] ?? null,
            'Pgender' => $validatedData['Pgender'] ?? null,
            'Page' => $validatedData['Page'] ?? null,
            'DrOID' => $validatedData['DrOID'] ?? null,
            'Tital' => $validatedData['Tital'] ?? null,
            'photo' => $validatedData['photo'] ?? null,
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
    public function doctorsList()
    {
        $patients = DB::connection('tenant')
            ->table('drreg')
            ->get();

        return response()->json($patients);
    }
}
