<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalInfo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Informations Médicales",
 *     description="Gestion des informations médicales des patients (diagnostics, traitements, analyses biologiques)"
 * )
 *
 * @OA\Schema(
 *     schema="MedicalInfo",
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
 *     schema="MedicalInfoRequest",
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
class MedicalInfoController extends Controller
{

        /**
     * @OA\Get(
     *     path="/medical-infos",
     *     operationId="getMedicalInfosList",
     *     tags={"Informations Médicales"},
     *     summary="Liste de toutes les informations médicales",
     *     description="Récupère la liste complète des informations médicales avec patients, médecins et stades de maladie",
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MedicalInfo")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $medicalInfos = MedicalInfo::with(['patient', 'doctor', 'kidneyDiseaseStage'])->get();

        return response()->json([
            'success' => true,
            'data' => $medicalInfos
        ]);
    }

    /**
     * @OA\Post(
     *     path="/medical-infos",
     *     operationId="storeMedicalInfo",
     *     tags={"Informations Médicales"},
     *     summary="Créer une information médicale",
     *     description="Enregistre une nouvelle information médicale pour un patient",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MedicalInfoRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Information médicale créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Information médicale créée avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalInfo")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */

    public function store(Request $request): JsonResponse
    {
        // Validation des donnu00e9es
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            'kidney_disease_stage_id' => 'nullable|exists:kidney_disease_stages,id',
            'diagnosis_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'on_dialysis' => 'boolean',
            'dialysis_start_date' => 'nullable|date',
            'current_treatment' => 'nullable|string',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'creatinine_level' => 'nullable|numeric',
            'gfr' => 'nullable|numeric',
            'albuminuria' => 'nullable|numeric',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'potassium_level' => 'nullable|numeric',
            'hemoglobin_level' => 'nullable|numeric',
        ]);

        $medicalInfo = MedicalInfo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Information mu00e9dicale cru00e9u00e9e avec succu00e8s',
            'data' => $medicalInfo
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/medical-infos/{id}",
     *     operationId="getMedicalInfoById",
     *     tags={"Informations Médicales"},
     *     summary="Détails d'une information médicale",
     *     description="Récupère les informations détaillées d'une fiche médicale",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'information médicale",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalInfo")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Information médicale non trouvée")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $medicalInfo = MedicalInfo::with(['patient', 'doctor', 'kidneyDiseaseStage'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $medicalInfo
        ]);
    }

    /**
     * @OA\Get(
     *     path="/medical-infos/patient/{patientId}",
     *     operationId="getMedicalInfoByPatient",
     *     tags={"Informations Médicales"},
     *     summary="Dernière information médicale d'un patient",
     *     description="Récupère la dernière information médicale enregistrée pour un patient spécifique",
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
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalInfo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucune information médicale trouvée pour ce patient",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucune information médicale trouvée pour ce patient")
     *         )
     *     )
     * )
     */
    public function getByPatient(int $patientId): JsonResponse
    {
        $medicalInfo = MedicalInfo::with(['doctor', 'kidneyDiseaseStage'])
            ->where('patient_id', $patientId)
            ->latest()
            ->first();

        if (!$medicalInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune information médicale trouvée pour ce patient'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medicalInfo
        ]);
    }

   /**
     * @OA\Put(
     *     path="/medical-infos/{id}",
     *     operationId="updateMedicalInfo",
     *     tags={"Informations Médicales"},
     *     summary="Mettre à jour une information médicale",
     *     description="Met à jour les informations d'une fiche médicale existante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'information médicale",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/MedicalInfoRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Information médicale mise à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Information médicale mise à jour avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalInfo")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Information médicale non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $medicalInfo = MedicalInfo::findOrFail($id);

        // Validation des donnu00e9es
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'user_id' => 'sometimes|exists:users,id',
            'kidney_disease_stage_id' => 'nullable|exists:kidney_disease_stages,id',
            'diagnosis_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'on_dialysis' => 'boolean',
            'dialysis_start_date' => 'nullable|date',
            'current_treatment' => 'nullable|string',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'creatinine_level' => 'nullable|numeric',
            'gfr' => 'nullable|numeric',
            'albuminuria' => 'nullable|numeric',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'potassium_level' => 'nullable|numeric',
            'hemoglobin_level' => 'nullable|numeric',
        ]);

        $medicalInfo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Information mu00e9dicale mise u00e0 jour avec succu00e8s',
            'data' => $medicalInfo
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/medical-infos/{id}",
     *     operationId="deleteMedicalInfo",
     *     tags={"Informations Médicales"},
     *     summary="Supprimer une information médicale",
     *     description="Supprime une information médicale du système",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'information médicale à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Information médicale supprimée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Information médicale supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Information médicale non trouvée")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $medicalInfo = MedicalInfo::findOrFail($id);
        $medicalInfo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Information mu00e9dicale supprimu00e9e avec succu00e8s'
        ]);
    }
}
