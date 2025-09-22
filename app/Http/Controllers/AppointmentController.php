<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TenantManager;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Display a listing of the appointments.
     */
    public function index()
    {
        $appointments = DB::connection('tenant')
            ->table('appoiment')
            ->paginate(20);

        return response()->json($appointments);
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'Date' => 'required',
            'POID' => 'required|string|max:50',
            'Name' => 'required|string|max:50',
            'Contact' => 'nullable|string|max:50',
            'DROID' => 'nullable|string|max:50',
            'DrName' => 'nullable|string|max:50',
        ]);
        $formattedDate = Carbon::createFromFormat('d/m/Y', $request->Date)
            ->format('Y-m-d H:i:s');
        // Insert appointment data
        $appointmentId = DB::connection('tenant')->table('appoiment')->insertGetId([
            'Date' => $formattedDate,
            'POID' => $validatedData['POID'],
            'Name' => $validatedData['Name'],
            'Contact' => $validatedData['Contact'] ?? null,
            'DROID' => $validatedData['DROID'] ?? null,
            'DrName' => $validatedData['DrName'] ?? null,
        ]);

        // Retrieve the created appointment
        $appointment = DB::connection('tenant')->table('appoiment')->where('id', $appointmentId)->first();

        // Return response
        return response()->json($appointment, 201);
    }

    /**
     * Display the specified appointment.
     */
    public function show($id)
    {
        $appointment = DB::connection('tenant')->table('appoiment')->where('id', $id)->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json($appointment);
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, $id)
    {
        // Check if appointment exists
        $appointment = DB::connection('tenant')->table('appoiment')->where('id', $id)->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Validate request data
        $validatedData = $request->validate([
            'Date' => 'required',
            'POID' => 'required|string|max:50',
            'Name' => 'required|string|max:50',
            'Contact' => 'nullable|string|max:50',
            'DROID' => 'nullable|string|max:50',
            'DrName' => 'nullable|string|max:50',
        ]);
        $formattedDate = Carbon::createFromFormat('d/m/Y', $request->Date)
            ->format('Y-m-d H:i:s');

        // Update appointment data
        DB::connection('tenant')->table('appoiment')->where('id', $id)->update([
            'Date' => $formattedDate,
            'POID' => $validatedData['POID'],
            'Name' => $validatedData['Name'],
            'Contact' => $validatedData['Contact'] ?? null,
            'DROID' => $validatedData['DROID'] ?? null,
            'DrName' => $validatedData['DrName'] ?? null,
        ]);

        // Retrieve the updated appointment
        $updatedAppointment = DB::connection('tenant')->table('appoiment')->where('id', $id)->first();

        return response()->json($updatedAppointment);
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy($id)
    {
        // Check if appointment exists
        $appointment = DB::connection('tenant')->table('appoiment')->where('id', $id)->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Delete the appointment
        DB::connection('tenant')->table('appoiment')->where('id', $id)->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}
