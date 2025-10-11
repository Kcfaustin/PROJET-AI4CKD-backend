<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="MedicalHistory",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="patient_id", type="integer", example=1),
 *     @OA\Property(property="diabetes", type="boolean", example=false),
 *     @OA\Property(property="hypertension", type="boolean", example=true),
 *     @OA\Property(property="heart_disease", type="boolean", example=false),
 *     @OA\Property(property="liver_disease", type="boolean", example=false),
 *     @OA\Property(property="autoimmune_disease", type="boolean", example=false),
 *     @OA\Property(property="smoking_status", type="string", enum={"non-fumeur", "occasionnel", "régulier"}, example="non-fumeur"),
 *     @OA\Property(property="bmi_status", type="string", enum={"sous-poids", "normal", "surpoids", "obèse"}, example="normal"),
 *     @OA\Property(property="alcohol_consumption", type="string", enum={"occasionnel", "modéré", "élevé"}, example="occasionnel"),
 *     @OA\Property(property="sedentary", type="boolean", example=false),
 *     @OA\Property(property="other_factors", type="string", nullable=true, example="Allergies alimentaires"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="MedicalHistoryRequest",
 *     type="object",
 *     required={"patient_id"},
 *     @OA\Property(property="patient_id", type="integer", example=1),
 *     @OA\Property(property="diabetes", type="boolean", example=false),
 *     @OA\Property(property="hypertension", type="boolean", example=true),
 *     @OA\Property(property="heart_disease", type="boolean", example=false),
 *     @OA\Property(property="liver_disease", type="boolean", example=false),
 *     @OA\Property(property="autoimmune_disease", type="boolean", example=false),
 *     @OA\Property(property="smoking_status", type="string", enum={"non-fumeur", "occasionnel", "régulier"}, example="non-fumeur"),
 *     @OA\Property(property="bmi_status", type="string", enum={"sous-poids", "normal", "surpoids", "obèse"}, example="normal"),
 *     @OA\Property(property="alcohol_consumption", type="string", enum={"occasionnel", "modéré", "élevé"}, example="occasionnel"),
 *     @OA\Property(property="sedentary", type="boolean", example=false),
 *     @OA\Property(property="other_factors", type="string", nullable=true, example="Allergies alimentaires")
 * )
 */


class MedicalHistoryController extends Controller
{

     /**
     * @OA\Get(
     *     path="/medical-histories",
     *     operationId="getMedicalHistoriesList",
     *     tags={"Antécédents Médicaux"},
     *     summary="Liste de tous les antécédents médicaux",
     *     description="Récupère la liste complète des antécédents médicaux avec les informations des patients",
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MedicalHistory")
     *             )
     *         )
     *     )
     * )
     */


    public function index(): JsonResponse
    {
        $medicalHistories = MedicalHistory::with('patient')->get();

        return response()->json([
            'success' => true,
            'data' => $medicalHistories
        ]);
    }

     /**
     * @OA\Post(
     *     path="/medical-histories",
     *     operationId="storeMedicalHistory",
     *     tags={"Antécédents Médicaux"},
     *     summary="Créer un antécédent médical",
     *     description="Enregistre un nouvel antécédent médical pour un patient",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MedicalHistoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Antécédent médical créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Antécédent médical créé avec succès."),
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalHistory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */


    public function store(Request $request): JsonResponse
    {
        // Validation des donnu00e9es
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'diabetes' => 'boolean',
            'hypertension' => 'boolean',
            'heart_disease' => 'boolean',
            'liver_disease' => 'boolean',
            'autoimmune_disease' => 'boolean',
            'smoking_status' => 'string|in:non-fumeur,occasionnel,ru00e9gulier',
            'bmi_status' => 'string|in:sous-poids,normal,surpoids,obu00e8se',
            'alcohol_consumption' => 'string|in:occasionnel,modu00e9ru00e9,u00e9levu00e9',
            'sedentary' => 'boolean',
            'other_factors' => 'nullable|string',
        ]);

        $medicalHistory = MedicalHistory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Antécédent médical créé avec succès.',
            'data' => $medicalHistory
        ], 201);
    }

     /**
     * @OA\Get(
     *     path="/medical-histories/{id}",
     *     operationId="getMedicalHistoryById",
     *     tags={"Antécédents Médicaux"},
     *     summary="Détails d'un antécédent médical",
     *     description="Récupère les informations détaillées d'un antécédent médical",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'antécédent médical",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalHistory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Antécédent médical non trouvé"
     *     )
     * )
     */

    public function show(int $id): JsonResponse
    {
        $medicalHistory = MedicalHistory::with('patient')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $medicalHistory
        ]);
    }

    /**
     * @OA\Get(
     *     path="/medical-histories/patient/{patientId}",
     *     operationId="getMedicalHistoryByPatient",
     *     tags={"Antécédents Médicaux"},
     *     summary="Dernier antécédent médical d'un patient",
     *     description="Récupère le dernier antécédent médical enregistré pour un patient spécifique",
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
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalHistory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucun antécédent médical trouvé pour ce patient",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucun antécédent médical trouvé pour ce patient.")
     *         )
     *     )
     * )
     */


    public function getByPatient(int $patientId): JsonResponse
    {
        $medicalHistory = MedicalHistory::where('patient_id', $patientId)->latest()->first();

        if (!$medicalHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun antécédent médical trouvé pour ce patient.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medicalHistory
        ]);
    }

     /**
     * @OA\Put(
     *     path="/medical-histories/{id}",
     *     operationId="updateMedicalHistory",
     *     tags={"Antécédents Médicaux"},
     *     summary="Mettre à jour un antécédent médical",
     *     description="Met à jour les informations d'un antécédent médical existant",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'antécédent médical",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/MedicalHistoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Antécédent médical mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Antécédent médical mis à jour avec succès."),
     *             @OA\Property(property="data", ref="#/components/schemas/MedicalHistory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Antécédent médical non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */


    public function update(Request $request, int $id): JsonResponse
    {
        $medicalHistory = MedicalHistory::findOrFail($id);

        // Validation des donnu00e9es
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'diabetes' => 'boolean',
            'hypertension' => 'boolean',
            'heart_disease' => 'boolean',
            'liver_disease' => 'boolean',
            'autoimmune_disease' => 'boolean',
            'smoking_status' => 'string|in:non-fumeur,occasionnel,ru00e9gulier',
            'bmi_status' => 'string|in:sous-poids,normal,surpoids,obu00e8se',
            'alcohol_consumption' => 'string|in:occasionnel,modu00e9ru00e9,u00e9levu00e9',
            'sedentary' => 'boolean',
            'other_factors' => 'nullable|string',
        ]);

        $medicalHistory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Antécédent médical mis à jour avec succès.',
            'data' => $medicalHistory
        ]);
    }
     /**
     * @OA\Delete(
     *     path="/medical-histories/{id}",
     *     operationId="deleteMedicalHistory",
     *     tags={"Antécédents Médicaux"},
     *     summary="Supprimer un antécédent médical",
     *     description="Supprime un antécédent médical du système",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'antécédent médical à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Antécédent médical supprimé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Antécédent médical supprimé avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Antécédent médical non trouvé"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $medicalHistory = MedicalHistory::findOrFail($id);
        $medicalHistory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Antécédent médical supprimé avec succès.'
        ]);
    }
}
