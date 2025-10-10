<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TenantManager;

class PrescriptionController extends Controller
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Display a listing of the prescriptions.
     */
    public function index(Request $request)
    {
        $query = DB::connection('tenant')
            ->table('prescription')
            ->distinct('POID');

        // Apply search if query parameter is present
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('PrescriptionNo', 'LIKE', "%{$search}%")
                    ->orWhere('History', 'LIKE', "%{$search}%")
                    ->orWhere('ItemName', 'LIKE', "%{$search}%")
                    ->orWhere('ContentName', 'LIKE', "%{$search}%")
                    ->orWhere('Name', 'LIKE', "%{$search}%");
            });
        }

        $prescriptions = $query->paginate(20);

        return response()->json($prescriptions);
    }


    /**
     * Store a newly created prescription.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'PrescriptionNo' => 'required|integer',
            'Date' => 'required|date',
            'POID' => 'required|integer',
            'History' => 'nullable|string|max:200',
            'ItemName' => 'nullable|string|max:200',
            'ContentName' => 'nullable|string|max:200',
            'Notes' => 'nullable|string|max:200',
            'Advice' => 'nullable|string|max:600',
            'ApDate' => 'nullable|date',
            'cc' => 'nullable|string|max:200',
            'cf' => 'nullable|string|max:200',
            'ge' => 'nullable|string|max:200',
            'inv' => 'nullable|string|max:200',
            'Name' => 'required|string|max:200',
        ]);

        // Insert prescription data
        $prescriptionId = DB::connection('tenant')->table('prescription')->insertGetId([
            'PrescriptionNo' => $validatedData['PrescriptionNo'],
            'Date' => $validatedData['Date'],
            'POID' => $validatedData['POID'],
            'History' => $validatedData['History'] ?? null,
            'ItemName' => $validatedData['ItemName'] ?? null,
            'ContentName' => $validatedData['ContentName'] ?? null,
            'Total' => $validatedData['Total'] ?? null,
            'Notes' => $validatedData['Notes'] ?? null,
            'Advice' => $validatedData['Advice'] ?? null,
            'ApDate' => $validatedData['ApDate'] ?? null,
            'cc' => $validatedData['cc'] ?? null,
            'cf' => $validatedData['cf'] ?? null,
            'ge' => $validatedData['ge'] ?? null,
            'inv' => $validatedData['inv'] ?? null,
            'Name' => $validatedData['Name'],
        ]);

        // Retrieve the created prescription
        $prescription = DB::connection('tenant')->table('prescription')->where('prescriptionOID', $prescriptionId)->first();

        // Return response
        return response()->json($prescription, 201);
    }


    /**
     * Display the specified prescription.
     */
    public function show($id)
    {
        $prescription = DB::connection('tenant')->table('prescription')->where('prescriptionOID', $id)->first();

        if (!$prescription) {
            return response()->json(['error' => 'Prescription not found'], 404);
        }

        return response()->json($prescription);
    }

    /**
     * Update the specified prescription.
     */
    public function update(Request $request, $id)
    {
        // Check if prescription exists
        $prescription = DB::connection('tenant')->table('prescription')->where('prescriptionOID', $id)->first();

        if (!$prescription) {
            return response()->json(['error' => 'Prescription not found'], 404);
        }

        // Validate request data
        $validatedData = $request->validate([
            'PrescriptionNo' => 'required|integer',
            'Date' => 'required|date',
            'POID' => 'required|integer',
            'History' => 'nullable|string|max:200',
            'ItemName' => 'nullable|string|max:200',
            'ContentName' => 'nullable|string|max:200',
            'Total' => 'nullable|string|max:50',
            'Notes' => 'nullable|string|max:200',
            'Advice' => 'nullable|string|max:600',
            'ApDate' => 'nullable|date',
            'cc' => 'nullable|string|max:200',
            'cf' => 'nullable|string|max:200',
            'ge' => 'nullable|string|max:200',
            'inv' => 'nullable|string|max:200',
            'Name' => 'required|string|max:200',
        ]);

        // Update prescription data
        DB::connection('tenant')->table('prescription')->where('prescriptionOID', $id)->update([
            'PrescriptionNo' => $validatedData['PrescriptionNo'],
            'Date' => $validatedData['Date'],
            'POID' => $validatedData['POID'],
            'History' => $validatedData['History'] ?? null,
            'ItemName' => $validatedData['ItemName'] ?? null,
            'ContentName' => $validatedData['ContentName'] ?? null,
            'Total' => $validatedData['Total'] ?? null,
            'Notes' => $validatedData['Notes'] ?? null,
            'Advice' => $validatedData['Advice'] ?? null,
            'ApDate' => $validatedData['ApDate'] ?? null,
            'cc' => $validatedData['cc'] ?? null,
            'cf' => $validatedData['cf'] ?? null,
            'ge' => $validatedData['ge'] ?? null,
            'inv' => $validatedData['inv'] ?? null,
            'Name' => $validatedData['Name'],
        ]);

        // Retrieve the updated prescription
        $updatedPrescription = DB::connection('tenant')->table('prescription')->where('prescriptionOID', $id)->first();

        return response()->json($updatedPrescription);
    }

    /**
     * Remove the specified prescription.
     */
    public function destroy($id)
    {
        // Check if prescription exists
        $prescription = DB::connection('tenant')->table('prescription')->where('prescriptionOID', $id)->first();

        if (!$prescription) {
            return response()->json(['error' => 'Prescription not found'], 404);
        }

        // Delete the prescription
        DB::connection('tenant')->table('prescription')->where('prescriptionOID', $id)->delete();

        return response()->json(['message' => 'Prescription deleted successfully']);
    }

    /**
     * Display a listing of the prescriptions for a specific patient.
     */
    public function prescriptionsByPatient($patientId, Request $request)
    {
        $query = DB::connection('tenant')
            ->table('prescription')
            ->where('POID', $patientId);

        // Apply search if query parameter is present
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('PrescriptionNo', 'LIKE', "%{$search}%")
                    ->orWhere('History', 'LIKE', "%{$search}%")
                    ->orWhere('ItemName', 'LIKE', "%{$search}%")
                    ->orWhere('ContentName', 'LIKE', "%{$search}%")
                    ->orWhere('Name', 'LIKE', "%{$search}%");
            });
        }

        $prescriptions = $query->paginate(20);

        return response()->json($prescriptions);
    }
}
