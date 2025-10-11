<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PatientRecord;
use App\Models\Patient;
use App\Models\KidneyDiseaseStage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Dossiers Médicaux",
 *     description="Gestion des dossiers médicaux des patients (diagnostics, traitements, historique médical complet)"
 * )
 *
 * @OA\Schema(
 *     schema="PatientRecord",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="patient_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=2, description="ID du médecin"),
 *     @OA\Property(property="kidney_disease_stage_id", type="integer", nullable=true, example=3),
 *     @OA\Property(property="diagnosis_date", type="string", format="date", nullable=true, example="2024-01-15"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Patient présente des symptômes légers"),
 *     @OA\Property(property="on_dialysis", type="boolean", example=false),
 *     @OA\Property(property="dialysis_start_date", type="string", format="date", nullable=true, example="2024-06-01"),
 *     @OA\Property(property="current_treatment", type="string", nullable=true, example="Inhibiteurs de l'ECA"),
 *     @OA\Property(property="medical_history", type="string", nullable=true, example="Hypertension depuis 5 ans"),
 *     @OA\Property(property="allergies", type="string", nullable=true, example="Pénicilline, Aspirine"),
 *     @OA\Property(property="blood_type", type="string", enum={"A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"}, nullable=true, example="A+"),
 *     @OA\Property(property="creatinine_level", type="number", format="float", nullable=true, example=1.2),
 *     @OA\Property(property="gfr", type="number", format="float", nullable=true, example=65.5, description="Taux de filtration glomérulaire"),
 *     @OA\Property(property="albuminuria", type="number", format="float", nullable=true, example=30.5),
 *     @OA\Property(property="blood_pressure_systolic", type="integer", nullable=true, example=130),
 *     @OA\Property(property="blood_pressure_diastolic", type="integer", nullable=true, example=85),
 *     @OA\Property(property="potassium_level", type="number", format="float", nullable=true, example=4.5),
 *     @OA\Property(property="hemoglobin_level", type="number", format="float", nullable=true, example=13.2),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="PatientRecordRequest",
 *     type="object",
 *     required={"patient_id", "user_id"},
 *     @OA\Property(property="patient_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="kidney_disease_stage_id", type="integer", nullable=true, example=3),
 *     @OA\Property(property="diagnosis_date", type="string", format="date", nullable=true, example="2024-01-15"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Patient présente des symptômes légers"),
 *     @OA\Property(property="on_dialysis", type="boolean", example=false),
 *     @OA\Property(property="dialysis_start_date", type="string", format="date", nullable=true, example="2024-06-01"),
 *     @OA\Property(property="current_treatment", type="string", nullable=true, example="Inhibiteurs de l'ECA"),
 *     @OA\Property(property="medical_history", type="string", nullable=true, example="Hypertension depuis 5 ans"),
 *     @OA\Property(property="allergies", type="string", nullable=true, example="Pénicilline, Aspirine"),
 *     @OA\Property(property="blood_type", type="string", enum={"A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"}, nullable=true, example="A+"),
 *     @OA\Property(property="creatinine_level", type="number", format="float", nullable=true, example=1.2),
 *     @OA\Property(property="gfr", type="number", format="float", nullable=true, example=65.5),
 *     @OA\Property(property="albuminuria", type="number", format="float", nullable=true, example=30.5),
 *     @OA\Property(property="blood_pressure_systolic", type="integer", nullable=true, example=130),
 *     @OA\Property(property="blood_pressure_diastolic", type="integer", nullable=true, example=85),
 *     @OA\Property(property="potassium_level", type="number", format="float", nullable=true, example=4.5),
 *     @OA\Property(property="hemoglobin_level", type="number", format="float", nullable=true, example=13.2)
 * )
 */
class PatientRecordController extends Controller
{

    /**
     * @OA\Get(
     *     path="/patient-records",
     *     operationId="getPatientRecordsList",
     *     tags={"Dossiers Médicaux"},
     *     summary="Liste de tous les dossiers médicaux",
     *     description="Récupère la liste complète des dossiers médicaux avec filtres optionnels",
     *     @OA\Parameter(
     *         name="patient_id",
     *         in="query",
     *         description="Filtrer par ID patient",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filtrer par ID médecin",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="kidney_disease_stage_id",
     *         in="query",
     *         description="Filtrer par stade de maladie rénale",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/PatientRecord")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = PatientRecord::with(['patient', 'doctor', 'kidneyDiseaseStage']);

        // Filtrer par patient si spécifié
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filtrer par médecin si spécifié
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrer par stade de maladie rénale si spécifié
        if ($request->has('kidney_disease_stage_id')) {
            $query->where('kidney_disease_stage_id', $request->kidney_disease_stage_id);
        }

        $patientRecords = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $patientRecords
        ]);
    }

    /**
     * @OA\Post(
     *     path="/patient-records",
     *     operationId="storePatientRecord",
     *     tags={"Dossiers Médicaux"},
     *     summary="Créer un dossier médical",
     *     description="Enregistre un nouveau dossier médical pour un patient. Le stade de maladie rénale est déterminé automatiquement basé sur le GFR si non spécifié.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PatientRecordRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dossier médical créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dossier médical créé avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/PatientRecord")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            'kidney_disease_stage_id' => 'nullable|exists:kidney_disease_stages,id',
            'diagnosis_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'on_dialysis' => 'boolean',
            'dialysis_start_date' => 'nullable|date',
            'current_treatment' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'creatinine_level' => 'nullable|numeric',
            'gfr' => 'nullable|numeric',
            'albuminuria' => 'nullable|numeric',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'potassium_level' => 'nullable|numeric',
            'hemoglobin_level' => 'nullable|numeric',
        ]);

        // Déterminer automatiquement le stade de la maladie rénale basé sur le DFG si non spécifié
        if (!isset($validated['kidney_disease_stage_id']) && isset($validated['gfr'])) {
            $gfr = $validated['gfr'];
            $stage = KidneyDiseaseStage::where('gfr_min', '<=', $gfr)
                ->where('gfr_max', '>=', $gfr)
                ->first();

            if ($stage) {
                $validated['kidney_disease_stage_id'] = $stage->id;
            }
        }

        $patientRecord = PatientRecord::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dossier médical créé avec succès',
            'data' => $patientRecord
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/patient-records/{id}",
     *     operationId="getPatientRecordById",
     *     tags={"Dossiers Médicaux"},
     *     summary="Détails d'un dossier médical",
     *     description="Récupère les informations détaillées d'un dossier médical",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du dossier médical",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/PatientRecord")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Dossier médical non trouvé")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $patientRecord = PatientRecord::with(['patient', 'doctor', 'kidneyDiseaseStage'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $patientRecord
        ]);
    }

     /**
     * @OA\Put(
     *     path="/patient-records/{id}",
     *     operationId="updatePatientRecord",
     *     tags={"Dossiers Médicaux"},
     *     summary="Mettre à jour un dossier médical",
     *     description="Met à jour les informations d'un dossier médical existant. Le stade de maladie rénale est recalculé automatiquement si le GFR est modifié.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du dossier médical",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/PatientRecordRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dossier médical mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dossier médical mis à jour avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/PatientRecord")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Dossier médical non trouvé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $patientRecord = PatientRecord::findOrFail($id);

        // Validation des données
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'user_id' => 'sometimes|exists:users,id',
            'kidney_disease_stage_id' => 'nullable|exists:kidney_disease_stages,id',
            'diagnosis_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'on_dialysis' => 'sometimes|boolean',
            'dialysis_start_date' => 'nullable|date',
            'current_treatment' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'creatinine_level' => 'nullable|numeric',
            'gfr' => 'nullable|numeric',
            'albuminuria' => 'nullable|numeric',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'potassium_level' => 'nullable|numeric',
            'hemoglobin_level' => 'nullable|numeric',
        ]);

        // Déterminer automatiquement le stade de la maladie rénale basé sur le DFG si modifié
        if (isset($validated['gfr']) && !isset($validated['kidney_disease_stage_id'])) {
            $gfr = $validated['gfr'];
            $stage = KidneyDiseaseStage::where('gfr_min', '<=', $gfr)
                ->where('gfr_max', '>=', $gfr)
                ->first();

            if ($stage) {
                $validated['kidney_disease_stage_id'] = $stage->id;
            }
        }

        $patientRecord->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dossier médical mis à jour avec succès',
            'data' => $patientRecord
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/patient-records/{id}",
     *     operationId="deletePatientRecord",
     *     tags={"Dossiers Médicaux"},
     *     summary="Supprimer un dossier médical",
     *     description="Supprime un dossier médical du système",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du dossier médical à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dossier médical supprimé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dossier médical supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Dossier médical non trouvé")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $patientRecord = PatientRecord::findOrFail($id);
        $patientRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dossier médical supprimé avec succès'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/patient-records/patient/{patientId}/latest",
     *     operationId="getLatestPatientRecord",
     *     tags={"Dossiers Médicaux"},
     *     summary="Dernier dossier médical d'un patient",
     *     description="Récupère le dernier dossier médical enregistré pour un patient spécifique",
     *     @OA\Parameter(
     *         name="patientId",
     *         in="path",
     *         description="ID du patient",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/PatientRecord")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucun dossier médical trouvé pour ce patient",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucun dossier médical trouvé pour ce patient")
     *         )
     *     )
     * )
     */
    public function getLatestForPatient(int $patientId): JsonResponse
    {
        $patient = Patient::findOrFail($patientId);

        $latestRecord = PatientRecord::where('patient_id', $patientId)
            ->with(['doctor', 'kidneyDiseaseStage'])
            ->latest()
            ->first();

        if (!$latestRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé pour ce patient'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $latestRecord
        ]);
    }

     /**
     * @OA\Get(
     *     path="/patient-records/statistics",
     *     operationId="getPatientRecordsStatistics",
     *     tags={"Dossiers Médicaux"},
     *     summary="Statistiques des dossiers médicaux",
     *     description="Retourne les statistiques globales des dossiers médicaux (total, dialyses, répartition par stade)",
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_records", type="integer", example=150),
     *                 @OA\Property(property="patients_on_dialysis", type="integer", example=25),
     *                 @OA\Property(
     *                     property="stage_distribution",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="stage", type="string", example="Stade 3"),
     *                         @OA\Property(property="count", type="integer", example=45)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function statistics(): JsonResponse
    {
        $totalRecords = PatientRecord::count();
        $patientsOnDialysis = PatientRecord::where('on_dialysis', true)->count();

        // Répartition par stade de maladie rénale
        $stageDistribution = PatientRecord::selectRaw('kidney_disease_stage_id, count(*) as count')
            ->whereNotNull('kidney_disease_stage_id')
            ->groupBy('kidney_disease_stage_id')
            ->get()
            ->map(function ($item) {
                $stage = KidneyDiseaseStage::find($item->kidney_disease_stage_id);
                return [
                    'stage' => $stage ? $stage->stage : 'Inconnu',
                    'count' => $item->count
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_records' => $totalRecords,
                'patients_on_dialysis' => $patientsOnDialysis,
                'stage_distribution' => $stageDistribution
            ]
        ]);
    }
}
