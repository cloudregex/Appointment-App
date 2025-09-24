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
    public function index(Request $request)
    {
        $query = DB::connection('tenant')
            ->table('appoiment');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('APPID', 'LIKE', "%{$search}%")
                    ->orWhere('DrName', 'LIKE', "%{$search}%")
                    ->orWhere('Name', 'LIKE', "%{$search}%");
            });
        }

        // Start date and End date filter
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate) {
            // If start date and end date are the same, treat it as a single date filter
            if ($startDate == $endDate) {
                $query->whereDate('Date', '=', $startDate);
            } else {
                $query->whereDate('Date', '>=', $startDate)
                    ->whereDate('Date', '<=', $endDate);
            }
        }

        $appointments = $query->paginate(20);

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
        $formattedDate = Carbon::createFromFormat('d-m-Y', $request->Date)
            ->format('Y-m-d');

        // Get current year
        $year = now()->year;

        // Find the latest APPID for this year
        $lastAppointment = DB::connection('tenant')->table('appoiment')
            ->where('APPID', 'like', $year . '-%')
            ->orderBy('APPOID', 'desc')
            ->first();

        if ($lastAppointment) {
            // Extract last increment
            $parts = explode('-', $lastAppointment->APPID);
            $lastIncrement = isset($parts[1]) ? (int)$parts[1] : 0;
            $nextIncrement = $lastIncrement + 1;
        } else {
            $nextIncrement = 1;
        }

        // Generate new APPID
        $newAppId = $year . '-' . $nextIncrement;

        // Insert appointment data
        $appointmentId = DB::connection('tenant')->table('appoiment')->insertGetId([
            'Date' => $formattedDate,
            'POID' => $validatedData['POID'],
            'Name' => $validatedData['Name'],
            'Contact' => $validatedData['Contact'] ?? null,
            'DROID' => $validatedData['DROID'] ?? null,
            'DrName' => $validatedData['DrName'] ?? null,
            'APPID' => $newAppId,
        ]);

        // Retrieve the created appointment
        $appointment = DB::connection('tenant')->table('appoiment')->where('APPOID', $appointmentId)->first();

        // Return response
        return response()->json($appointment, 201);
    }

    /**
     * Display the specified appointment.
     */
    public function show($id)
    {
        $appointment = DB::connection('tenant')->table('appoiment')->where('APPOID', $id)->first();

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
        $appointment = DB::connection('tenant')->table('appoiment')->where('APPOID', $id)->first();

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
        // Handle Date formatting conditionally
        try {
            $formattedDate = Carbon::createFromFormat('d-m-Y', $request->Date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                $formattedDate = Carbon::parse($request->Date)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format. Use d-m-Y or Y-m-d'], 422);
            }
        }

        // Update appointment data
        DB::connection('tenant')->table('appoiment')->where('APPOID', $id)->update([
            'Date' => $formattedDate,
            'POID' => $validatedData['POID'],
            'Name' => $validatedData['Name'],
            'Contact' => $validatedData['Contact'] ?? null,
            'DROID' => $validatedData['DROID'] ?? null,
            'DrName' => $validatedData['DrName'] ?? null,
        ]);

        // Retrieve the updated appointment
        $updatedAppointment = DB::connection('tenant')->table('appoiment')->where('APPOID', $id)->first();

        return response()->json($updatedAppointment);
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy($id)
    {
        // Check if appointment exists
        $appointment = DB::connection('tenant')->table('appoiment')->where('APPOID', $id)->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Delete the appointment
        DB::connection('tenant')->table('appoiment')->where('APPOID', $id)->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}
