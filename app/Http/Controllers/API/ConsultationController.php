<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Consultations",
 *     description="Gestion des consultations médicales"
 * )
 */

class ConsultationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/consultations",
     *     tags={"Consultations"},
     *     summary="Liste des consultations",
     *     description="Retourne la liste des consultations avec filtres optionnels",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filtrer par ID du médecin",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="patient_id",
     *         in="query",
     *         description="Filtrer par ID du patient",
     *         required=false,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filtrer par date (format: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-10-15")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des consultations récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="patient_id", type="integer", example=5),
     *                     @OA\Property(property="user_id", type="integer", example=2),
     *                     @OA\Property(property="consultation_date", type="string", format="date-time", example="2025-10-15 14:30:00"),
     *                     @OA\Property(property="reason", type="string", example="Contrôle de routine"),
     *                     @OA\Property(property="clinical_notes", type="string", example="Patient en bonne santé générale"),
     *                     @OA\Property(property="decision", type="string", example="Poursuivre le traitement actuel"),
     *                     @OA\Property(property="patient_record_id", type="integer", example=3),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(
     *                         property="patient",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="first_name", type="string", example="Jean"),
     *                         @OA\Property(property="last_name", type="string", example="Dupont"),
     *                         @OA\Property(property="email", type="string", example="jean.dupont@example.com")
     *                     ),
     *                     @OA\Property(
     *                         property="doctor",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="first_name", type="string", example="Marie"),
     *                         @OA\Property(property="last_name", type="string", example="Martin"),
     *                         @OA\Property(property="specialization", type="string", example="Cardiologie")
     *                     ),
     *                     @OA\Property(
     *                         property="patient_record",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="blood_type", type="string", example="A+")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Consultation::with(['patient', 'doctor', 'patientRecord']);

        // Filtrer par médecin si spécifié
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrer par patient si spécifié
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filtrer par date si spécifiée
        if ($request->has('date')) {
            $date = $request->date;
            $query->whereDate('consultation_date', $date);
        }

        $consultations = $query->orderBy('consultation_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/consultations",
     *     tags={"Consultations"},
     *     summary="Créer une nouvelle consultation",
     *     description="Enregistre une nouvelle consultation médicale",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_id", "user_id", "consultation_date"},
     *             @OA\Property(property="patient_id", type="integer", description="ID du patient", example=5),
     *             @OA\Property(property="user_id", type="integer", description="ID du médecin", example=2),
     *             @OA\Property(property="consultation_date", type="string", format="date-time", description="Date et heure de la consultation", example="2025-10-15 14:30:00"),
     *             @OA\Property(property="reason", type="string", description="Motif de la consultation", example="Contrôle de routine"),
     *             @OA\Property(property="clinical_notes", type="string", description="Notes cliniques", example="Patient en bonne santé générale. Tension artérielle normale."),
     *             @OA\Property(property="decision", type="string", description="Décision médicale", example="Poursuivre le traitement actuel, revoir dans 3 mois"),
     *             @OA\Property(property="patient_record_id", type="integer", description="ID du dossier patient associé", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Consultation créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Consultation créée avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="consultation_date", type="string", format="date-time", example="2025-10-15 14:30:00"),
     *                 @OA\Property(property="reason", type="string", example="Contrôle de routine"),
     *                 @OA\Property(property="clinical_notes", type="string", example="Patient en bonne santé générale"),
     *                 @OA\Property(property="decision", type="string", example="Poursuivre le traitement actuel"),
     *                 @OA\Property(property="patient_record_id", type="integer", example=3),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="patient_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The patient id field is required.")
     *                 )
     *             )
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
            'consultation_date' => 'required|date',
            'reason' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
            'decision' => 'nullable|string',
            'patient_record_id' => 'nullable|exists:patient_records,id',
        ]);

        $consultation = Consultation::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Consultation créée avec succès',
            'data' => $consultation
        ], 201);
    }

   /**
     * @OA\Get(
     *     path="/api/consultations/{id}",
     *     tags={"Consultations"},
     *     summary="Détails d'une consultation",
     *     description="Retourne les informations détaillées d'une consultation spécifique avec les relations",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la consultation",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la consultation récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="consultation_date", type="string", format="date-time", example="2025-10-15 14:30:00"),
     *                 @OA\Property(property="reason", type="string", example="Contrôle de routine"),
     *                 @OA\Property(property="clinical_notes", type="string", example="Patient en bonne santé générale"),
     *                 @OA\Property(property="decision", type="string", example="Poursuivre le traitement actuel"),
     *                 @OA\Property(property="patient_record_id", type="integer", example=3),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="first_name", type="string", example="Jean"),
     *                     @OA\Property(property="last_name", type="string", example="Dupont")
     *                 ),
     *                 @OA\Property(
     *                     property="doctor",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="first_name", type="string", example="Marie"),
     *                     @OA\Property(property="last_name", type="string", example="Martin")
     *                 ),
     *                 @OA\Property(
     *                     property="patient_record",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Consultation] 1")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $consultation = Consultation::with(['patient', 'doctor', 'patientRecord'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $consultation
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/consultations/{id}",
     *     tags={"Consultations"},
     *     summary="Mettre à jour une consultation",
     *     description="Met à jour les informations d'une consultation existante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la consultation",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=5),
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="consultation_date", type="string", format="date-time", example="2025-10-15 14:30:00"),
     *             @OA\Property(property="reason", type="string", example="Contrôle de routine"),
     *             @OA\Property(property="clinical_notes", type="string", example="Patient en bonne santé générale"),
     *             @OA\Property(property="decision", type="string", example="Poursuivre le traitement actuel"),
     *             @OA\Property(property="patient_record_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Consultation mise à jour avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="consultation_date", type="string", format="date-time"),
     *                 @OA\Property(property="reason", type="string"),
     *                 @OA\Property(property="clinical_notes", type="string"),
     *                 @OA\Property(property="decision", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation non trouvée"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $consultation = Consultation::findOrFail($id);

        // Validation des données
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'user_id' => 'sometimes|exists:users,id',
            'consultation_date' => 'sometimes|date',
            'reason' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
            'decision' => 'nullable|string',
            'patient_record_id' => 'nullable|exists:patient_records,id',
        ]);

        $consultation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Consultation mise à jour avec succès',
            'data' => $consultation
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/consultations/{id}",
     *     tags={"Consultations"},
     *     summary="Supprimer une consultation",
     *     description="Supprime une consultation de la base de données",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la consultation",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Consultation supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation non trouvée"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Consultation supprimée avec succès'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/consultations/patient/{patientId}/history",
     *     tags={"Consultations"},
     *     summary="Historique des consultations d'un patient",
     *     description="Récupère l'historique complet des consultations d'un patient spécifique",
     *     @OA\Parameter(
     *         name="patientId",
     *         in="path",
     *         description="ID du patient",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historique récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="first_name", type="string", example="Jean"),
     *                     @OA\Property(property="last_name", type="string", example="Dupont"),
     *                     @OA\Property(property="email", type="string", example="jean.dupont@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+229123456789")
     *                 ),
     *                 @OA\Property(
     *                     property="consultations",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="consultation_date", type="string", format="date-time", example="2025-10-15 14:30:00"),
     *                         @OA\Property(property="reason", type="string", example="Contrôle de routine"),
     *                         @OA\Property(property="clinical_notes", type="string", example="Patient en bonne santé"),
     *                         @OA\Property(property="decision", type="string", example="Poursuivre le traitement"),
     *                         @OA\Property(
     *                             property="doctor",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="first_name", type="string", example="Marie"),
     *                             @OA\Property(property="last_name", type="string", example="Martin"),
     *                             @OA\Property(property="specialization", type="string", example="Cardiologie")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient non trouvé"
     *     )
     * )
     */
    public function getPatientHistory(int $patientId): JsonResponse
    {
        $patient = Patient::findOrFail($patientId);

        $consultations = Consultation::where('patient_id', $patientId)
            ->with(['doctor', 'patientRecord'])
            ->orderBy('consultation_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => $patient,
                'consultations' => $consultations
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/consultations/statistics",
     *     tags={"Consultations"},
     *     summary="Statistiques des consultations",
     *     description="Retourne des statistiques globales sur les consultations",
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_consultations", type="integer", description="Nombre total de consultations", example=250),
     *                 @OA\Property(
     *                     property="consultations_by_doctor",
     *                     type="array",
     *                     description="Répartition des consultations par médecin",
     *                     @OA\Items(
     *                         @OA\Property(property="doctor_id", type="integer", example=2),
     *                         @OA\Property(property="doctor_name", type="string", example="Dr. Marie Martin"),
     *                         @OA\Property(property="count", type="integer", example=45)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="consultations_by_day",
     *                     type="array",
     *                     description="Nombre de consultations par jour (30 derniers jours)",
     *                     @OA\Items(
     *                         @OA\Property(property="date", type="string", format="date", example="2025-10-15"),
     *                         @OA\Property(property="count", type="integer", example=8)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function statistics(): JsonResponse
    {
        $totalConsultations = Consultation::count();

        // Consultations par médecin
        $consultationsByDoctor = Consultation::selectRaw('user_id, count(*) as count')
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                $doctor = User::find($item->user_id);
                return [
                    'doctor_id' => $item->user_id,
                    'doctor_name' => $doctor ? $doctor->first_name . ' ' . $doctor->last_name : 'Inconnu',
                    'count' => $item->count
                ];
            });

        // Consultations par jour (30 derniers jours)
        $consultationsByDay = Consultation::selectRaw('DATE(consultation_date) as date, count(*) as count')
            ->whereDate('consultation_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_consultations' => $totalConsultations,
                'consultations_by_doctor' => $consultationsByDoctor,
                'consultations_by_day' => $consultationsByDay
            ]
        ]);
    }
}
