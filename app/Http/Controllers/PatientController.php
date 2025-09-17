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
            'Pname' => 'nullable|string|max:50',
            'Paddress' => 'nullable|string|max:200',
            'Pcontact' => 'nullable|string|max:50',
            'Pgender' => 'nullable|string|max:50',
            'Page' => 'nullable|string|max:50',
            'DrOID' => 'nullable|integer',
            'Tital' => 'nullable|string|max:50',
            'photo' => 'nullable|string',
        ]);
        // Step 1: Extract initials from Patient Name (Ex: "Prashant Nale" => "PN")
        $nameParts = explode(" ", $validatedData['Pname'] ?? '');
        $initials = '';
        foreach ($nameParts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        if (empty($initials)) {
            $initials = "XX"; // fallback if no name
        }

        // Step 2: Current year & month
        $year  = date('Y');
        $month = date('m');

        // Step 3: Find last DrOID for this month
        $lastEntry = DB::connection('tenant')
            ->table('pateintreg')
            ->where('DrOID', 'like', $initials . '-' . $year . '-' . $month . '-%')
            ->orderByDesc('POID')
            ->first();

        // Step 4: Increment last sequence
        $lastNumber = 0;
        if ($lastEntry) {
            $parts = explode("-", $lastEntry->DrOID);
            $lastNumber = intval(end($parts));
        }
        $nextNumber = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT);

        // Step 5: Generate unique DrOID
        $generatedDrOID = $initials . '-' . $year . '-' . $month . '-' . $nextNumber;
        // Insert patient data
        $patientId = DB::connection('tenant')->table('pateintreg')->insertGetId([
            'Pname' => $validatedData['Pname'] ?? null,
            'Paddress' => $validatedData['Paddress'] ?? null,
            'Pcontact' => $validatedData['Pcontact'] ?? null,
            'Pgender' => $validatedData['Pgender'] ?? null,
            'Page' => $validatedData['Page'] ?? null,
            'DrOID' => $validatedData['DrOID'] ?? null,
            'RegNo' => $generatedDrOID,
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
