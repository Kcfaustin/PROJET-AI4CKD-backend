<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Affiche la liste des rendez-vous.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::query()
            ->select([
                'appointments.id',
                'appointments.date',
                'appointments.time',
                'appointments.duration',
                'appointments.type',
                'appointments.status',
                'appointments.notes',
                'patients.id as patient_id',
                DB::raw('CONCAT(patients.first_name, " ", patients.last_name) as patient_name'),
                'patients.phone',
                'patients.status as patient_status',
                'users.id as doctor_id',
                DB::raw('CONCAT(users.first_name, " ", users.last_name) as doctor_name'),
                'users.specialization as specialty'
            ])
            ->leftJoin('patients', 'patients.id', '=', 'appointments.patient_id')
            ->leftJoin('users', 'users.id', '=', 'appointments.user_id');

        // Filtrer par médecin si spécifié
        $doctorId = $request->input('doctor_id');
        if ($doctorId && $doctorId !== 'all' && $doctorId !== 'undefined') {
            $query->where('users.id', $doctorId);
        }

        // Filtrer par statut si spécifié
        $status = $request->input('status');
        if ($status && $status !== 'all' && $status !== 'undefined') {
            $query->where('appointments.status', $status);
        }

        // Filtrer par type si spécifié
        $type = $request->input('type');
        if ($type && $type !== 'all' && $type !== 'undefined') {
            $query->where('appointments.type', $type);
        }

        // Filtrer par date si spécifiée (attendu en Y-m-d depuis formatToYMD)
        $date = $request->input('date');
        if ($date && $date !== 'undefined') {
            $query->where('appointments.date', $date);
        }

        // Recherche globale
        $search = $request->input('search');
        if ($search && $search !== 'undefined') {
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('CONCAT(patients.first_name, " ", patients.last_name)'), 'like', "%$search%")
                ->orWhere(DB::raw('CONCAT(users.first_name, " ", users.last_name)'), 'like', "%$search%");
            });
        }

        $appointments = $query->orderBy('appointments.date', 'asc')
                            ->orderBy('appointments.time', 'asc')
                            ->get()
                            ->map(function ($appointment) {
                                return [
                                    'id' => $appointment->id,
                                    'patientName' => $appointment->patient_name,
                                    'patientId' => $appointment->patient_id,
                                    'date' => Carbon::parse($appointment->date)->format('d/m/Y'), // ✅ Format d/m/Y
                                    'time' => $appointment->time,
                                    'duration' => $appointment->duration,
                                    'type' => $appointment->type,
                                    'doctor' => $appointment->doctor_name,
                                    'status' => $appointment->status,
                                    'notes' => $appointment->notes,
                                    'patient' => [
                                        'phone' => $appointment->phone,
                                        'status' => $appointment->patient_status
                                    ]
                                ];
                            });

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Enregistre un nouveau rendez-vous.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date_format:d/m/Y',  // Format reçu du frontend
            'time' => 'required|date_format:H:i',
            'duration' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string|in:Planifié,Confirmé,Annulé,Terminé',
            'notes' => 'nullable|string'
        ]);

        // Convertir la date pour MySQL
        $validated['date'] = Carbon::createFromFormat('d/m/Y', $validated['date'])->format('Y-m-d');

        // Vérifier si le médecin est disponible à cette heure
        $conflictingAppointment = Appointment::where('user_id', $validated['user_id'])
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->where('status', '!=', 'Annulé')
            ->first();

        if ($conflictingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'Le médecin a déjà un rendez-vous à cette heure.'
            ], 422);
        }

        // Créer le rendez-vous
        $appointment = Appointment::create($validated);

        // Récupérer le patient et le médecin pour la réponse
        $patient = Patient::find($validated['patient_id']);
        $doctor = User::find($validated['user_id']);

        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous créé avec succès',
            'data' => [
                'id' => $appointment->id,
                'patientName' => $patient->first_name . ' ' . $patient->last_name,
                'patientId' => $patient->id,
                'date' => Carbon::parse($appointment->date)->format('d/m/Y'), // ✅ Format d/m/Y
                'time' => $appointment->time,
                'duration' => $appointment->duration,
                'type' => $appointment->type,
                'doctor' => $doctor->first_name . ' ' . $doctor->last_name,
                'status' => $appointment->status,
                'notes' => $appointment->notes,
                'patient' => [
                    'phone' => $patient->phone,
                    'status' => $patient->status
                ]
            ]
        ], 201);
    }

    /**
     * Affiche les détails d’un rendez-vous spécifique.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $appointment = Appointment::with(['patient', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $appointment->id,
                'patientName' => $appointment->patient->first_name . ' ' . $appointment->patient->last_name,
                'patientId' => $appointment->patient_id,
                'date' => Carbon::parse($appointment->date)->format('d/m/Y'), // ✅ Format d/m/Y
                'time' => $appointment->time,
                'duration' => $appointment->duration,
                'type' => $appointment->type,
                'doctor' => $appointment->user->first_name . ' ' . $appointment->user->last_name,
                'status' => $appointment->status,
                'notes' => $appointment->notes,
                'patient' => [
                    'phone' => $appointment->patient->phone,
                    'status' => $appointment->patient->status
                ]
            ]
        ]);
    }

    /**
     * Met à jour les informations d’un rendez-vous.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        // Validation des données
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'user_id' => 'sometimes|exists:users,id',
            'date' => 'sometimes|date_format:d/m/Y', // ✅ Attend d/m/Y du frontend
            'time' => 'sometimes|date_format:H:i',
            'duration' => 'sometimes|string',
            'type' => 'sometimes|string',
            'status' => 'sometimes|string|in:Planifié,Confirmé,Annulé,Terminé',
            'notes' => 'nullable|string'
        ]);

        // Convertir la date pour MySQL si présente
        if (isset($validated['date'])) {
            $validated['date'] = Carbon::createFromFormat('d/m/Y', $validated['date'])->format('Y-m-d');
        }

        // Vérification des conflits
        if ($request->hasAny(['date', 'time', 'user_id']) && ($request->status ?? $appointment->status) !== 'Annulé') {
            $doctorId = $request->user_id ?? $appointment->user_id;
            $date = $validated['date'] ?? $appointment->date;
            $time = $validated['time'] ?? $appointment->time;

            $conflict = Appointment::where('user_id', $doctorId)
                ->where('date', $date)
                ->where('time', $time)
                ->where('status', '!=', 'Annulé')
                ->where('id', '!=', $id)
                ->first();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le médecin a déjà un rendez-vous à cette heure.'
                ], 422);
            }
        }

        // Mise à jour
        $appointment->update($validated);
        $appointment->refresh();

        $patient = $appointment->patient;
        $doctor = $appointment->user;

        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous mis à jour avec succès',
            'data' => [
                'id' => $appointment->id,
                'patientName' => $patient ? $patient->first_name . ' ' . $patient->last_name : null,
                'patientId' => $appointment->patient_id,
                'date' => Carbon::parse($appointment->date)->format('d/m/Y'), // ✅ Format d/m/Y
                'time' => $appointment->time,
                'duration' => $appointment->duration,
                'type' => $appointment->type,
                'doctor' => $doctor ? $doctor->first_name . ' ' . $doctor->last_name : null,
                'status' => $appointment->status,
                'notes' => $appointment->notes,
                'patient' => $patient ? [
                    'phone' => $patient->phone,
                    'status' => $patient->status
                ] : null
            ]
        ]);
    }

    /**
     * Supprime un rendez-vous.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous supprimé avec succès'
        ]);
    }

    /**
     * Retourne les statistiques des rendez-vous.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        $stats = Appointment::select(
            DB::raw('count(*) as total_appointments'),
            DB::raw('SUM(CASE WHEN status = "Planifié" THEN 1 ELSE 0 END) as planned_appointments'),
            DB::raw('SUM(CASE WHEN status = "Confirmé" THEN 1 ELSE 0 END) as confirmed_appointments'),
            DB::raw('SUM(CASE WHEN status = "Annulé" THEN 1 ELSE 0 END) as canceled_appointments'),
            DB::raw('SUM(CASE WHEN status = "Terminé" THEN 1 ELSE 0 END) as completed_appointments')
        )->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_appointments' => $stats->total_appointments,
                'planned_appointments' => $stats->planned_appointments,
                'confirmed_appointments' => $stats->confirmed_appointments,
                'canceled_appointments' => $stats->canceled_appointments,
                'completed_appointments' => $stats->completed_appointments,
                'completion_rate' => $stats->total_appointments > 0
                    ? round(($stats->completed_appointments / $stats->total_appointments) * 100, 2)
                    : 0
            ]
        ]);
    }
}
