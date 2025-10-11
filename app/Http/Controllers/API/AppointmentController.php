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

/**
 * @OA\Tag(
 *     name="Rendez-vous",
 *     description="Gestion des rendez-vous médicaux"
 * )
 */
class AppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/appointments",
     *     tags={"Rendez-vous"},
     *     summary="Liste des rendez-vous",
     *     description="Retourne la liste des rendez-vous avec filtres optionnels",
     *     @OA\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         description="Filtrer par ID du médecin",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrer par statut",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Planifié", "Confirmé", "Annulé", "Terminé"})
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtrer par type de consultation",
     *         required=false,
     *         @OA\Schema(type="string", example="Consultation")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filtrer par date (format: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-10-15")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Rechercher par nom de patient ou médecin",
     *         required=false,
     *         @OA\Schema(type="string", example="Jean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des rendez-vous récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="patientName", type="string", example="Jean Dupont"),
     *                     @OA\Property(property="patientId", type="integer", example=5),
     *                     @OA\Property(property="date", type="string", example="15/10/2025"),
     *                     @OA\Property(property="time", type="string", example="14:30"),
     *                     @OA\Property(property="duration", type="string", example="30 min"),
     *                     @OA\Property(property="type", type="string", example="Consultation"),
     *                     @OA\Property(property="doctor", type="string", example="Dr. Marie Martin"),
     *                     @OA\Property(property="status", type="string", example="Confirmé"),
     *                     @OA\Property(property="notes", type="string", example="Premier rendez-vous"),
     *                     @OA\Property(
     *                         property="patient",
     *                         type="object",
     *                         @OA\Property(property="phone", type="string", example="+229123456789"),
     *                         @OA\Property(property="status", type="string", example="actif")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/appointments",
     *     tags={"Rendez-vous"},
     *     summary="Créer un nouveau rendez-vous",
     *     description="Enregistre un nouveau rendez-vous médical",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_id", "user_id", "date", "time", "duration", "type", "status"},
     *             @OA\Property(property="patient_id", type="integer", description="ID du patient", example=5),
     *             @OA\Property(property="user_id", type="integer", description="ID du médecin", example=2),
     *             @OA\Property(property="date", type="string", description="Date du rendez-vous (format: dd/mm/yyyy)", example="15/10/2025"),
     *             @OA\Property(property="time", type="string", description="Heure du rendez-vous (format: HH:mm)", example="14:30"),
     *             @OA\Property(property="duration", type="string", description="Durée du rendez-vous", example="30 min"),
     *             @OA\Property(property="type", type="string", description="Type de consultation", example="Consultation"),
     *             @OA\Property(property="status", type="string", enum={"Planifié", "Confirmé", "Annulé", "Terminé"}, example="Planifié"),
     *             @OA\Property(property="notes", type="string", description="Notes additionnelles", example="Premier rendez-vous")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rendez-vous créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rendez-vous créé avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patientName", type="string", example="Jean Dupont"),
     *                 @OA\Property(property="patientId", type="integer", example=5),
     *                 @OA\Property(property="date", type="string", example="15/10/2025"),
     *                 @OA\Property(property="time", type="string", example="14:30"),
     *                 @OA\Property(property="duration", type="string", example="30 min"),
     *                 @OA\Property(property="type", type="string", example="Consultation"),
     *                 @OA\Property(property="doctor", type="string", example="Dr. Marie Martin"),
     *                 @OA\Property(property="status", type="string", example="Planifié"),
     *                 @OA\Property(property="notes", type="string", example="Premier rendez-vous"),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="phone", type="string", example="+229123456789"),
     *                     @OA\Property(property="status", type="string", example="actif")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation ou conflit d'horaire",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Le médecin a déjà un rendez-vous à cette heure.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/appointments/{id}",
     *     tags={"Rendez-vous"},
     *     summary="Détails d'un rendez-vous",
     *     description="Retourne les informations détaillées d'un rendez-vous spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du rendez-vous",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du rendez-vous récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patientName", type="string", example="Jean Dupont"),
     *                 @OA\Property(property="patientId", type="integer", example=5),
     *                 @OA\Property(property="date", type="string", example="15/10/2025"),
     *                 @OA\Property(property="time", type="string", example="14:30"),
     *                 @OA\Property(property="duration", type="string", example="30 min"),
     *                 @OA\Property(property="type", type="string", example="Consultation"),
     *                 @OA\Property(property="doctor", type="string", example="Dr. Marie Martin"),
     *                 @OA\Property(property="status", type="string", example="Confirmé"),
     *                 @OA\Property(property="notes", type="string", example="Premier rendez-vous"),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="phone", type="string", example="+229123456789"),
     *                     @OA\Property(property="status", type="string", example="actif")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rendez-vous non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Appointment] 1")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/appointments/{id}",
     *     tags={"Rendez-vous"},
     *     summary="Mettre à jour un rendez-vous",
     *     description="Met à jour les informations d'un rendez-vous existant",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du rendez-vous",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=5),
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="date", type="string", description="Format: dd/mm/yyyy", example="15/10/2025"),
     *             @OA\Property(property="time", type="string", description="Format: HH:mm", example="14:30"),
     *             @OA\Property(property="duration", type="string", example="30 min"),
     *             @OA\Property(property="type", type="string", example="Consultation"),
     *             @OA\Property(property="status", type="string", enum={"Planifié", "Confirmé", "Annulé", "Terminé"}, example="Confirmé"),
     *             @OA\Property(property="notes", type="string", example="Notes mises à jour")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rendez-vous mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rendez-vous mis à jour avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patientName", type="string", example="Jean Dupont"),
     *                 @OA\Property(property="patientId", type="integer", example=5),
     *                 @OA\Property(property="date", type="string", example="15/10/2025"),
     *                 @OA\Property(property="time", type="string", example="14:30"),
     *                 @OA\Property(property="duration", type="string", example="30 min"),
     *                 @OA\Property(property="type", type="string", example="Consultation"),
     *                 @OA\Property(property="doctor", type="string", example="Dr. Marie Martin"),
     *                 @OA\Property(property="status", type="string", example="Confirmé")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rendez-vous non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation ou conflit d'horaire"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/appointments/{id}",
     *     tags={"Rendez-vous"},
     *     summary="Supprimer un rendez-vous",
     *     description="Supprime un rendez-vous de la base de données",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du rendez-vous",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rendez-vous supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rendez-vous supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rendez-vous non trouvé"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/appointments/statistics",
     *     tags={"Rendez-vous"},
     *     summary="Statistiques des rendez-vous",
     *     description="Retourne des statistiques globales sur les rendez-vous",
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_appointments", type="integer", description="Nombre total de rendez-vous", example=150),
     *                 @OA\Property(property="planned_appointments", type="integer", description="Rendez-vous planifiés", example=30),
     *                 @OA\Property(property="confirmed_appointments", type="integer", description="Rendez-vous confirmés", example=50),
     *                 @OA\Property(property="canceled_appointments", type="integer", description="Rendez-vous annulés", example=20),
     *                 @OA\Property(property="completed_appointments", type="integer", description="Rendez-vous terminés", example=50),
     *                 @OA\Property(property="completion_rate", type="number", format="float", description="Taux de complétion en pourcentage", example=33.33)
     *             )
     *         )
     *     )
     * )
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
