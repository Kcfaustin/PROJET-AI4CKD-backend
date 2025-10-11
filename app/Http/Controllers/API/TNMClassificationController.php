<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TNMClassification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Classifications TNM",
 *     description="Gestion des classifications TNM (Tumor, Node, Metastasis) pour l'évaluation des cancers"
 * )
 *
 * @OA\Schema(
 *     schema="TNMClassification",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="patient_id", type="integer", example=1),
 *     @OA\Property(property="t_stage", type="string", example="T2", description="Stade de la tumeur primaire"),
 *     @OA\Property(property="n_stage", type="string", example="N1", description="Stade de l'atteinte ganglionnaire"),
 *     @OA\Property(property="m_stage", type="string", example="M0", description="Stade des métastases à distance"),
 *     @OA\Property(property="overall_stage", type="string", example="Stage IIA", description="Stade global du cancer"),
 *     @OA\Property(property="grade", type="string", example="G2", description="Grade histologique"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Tumeur localisée, bon pronostic"),
 *     @OA\Property(property="classification_date", type="string", format="date", example="2024-02-20"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="TNMClassificationRequest",
 *     type="object",
 *     required={"patient_id", "t_stage", "n_stage", "m_stage", "overall_stage", "grade", "classification_date"},
 *     @OA\Property(property="patient_id", type="integer", example=1),
 *     @OA\Property(property="t_stage", type="string", example="T2", description="Stade T (T0, T1, T2, T3, T4)"),
 *     @OA\Property(property="n_stage", type="string", example="N1", description="Stade N (N0, N1, N2, N3)"),
 *     @OA\Property(property="m_stage", type="string", example="M0", description="Stade M (M0, M1)"),
 *     @OA\Property(property="overall_stage", type="string", example="Stage IIA", description="Stade global (Stage I, IIA, IIB, IIIA, etc.)"),
 *     @OA\Property(property="grade", type="string", example="G2", description="Grade histologique (G1, G2, G3, G4)"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Tumeur localisée, bon pronostic"),
 *     @OA\Property(property="classification_date", type="string", format="date", example="2024-02-20")
 * )
 */
class TNMClassificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tnm-classifications",
     *     operationId="getTNMClassificationsList",
     *     tags={"Classifications TNM"},
     *     summary="Liste de toutes les classifications TNM",
     *     description="Récupère la liste complète des classifications TNM avec les informations des patients",
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TNMClassification")
     *             )
     *         )
     *     )
     * )
     */

    public function index(): JsonResponse
    {
        $tnmClassifications = TNMClassification::with('patient')->get();

        return response()->json([
            'success' => true,
            'data' => $tnmClassifications
        ]);
    }

    /**
     * @OA\Post(
     *     path="/tnm-classifications",
     *     operationId="storeTNMClassification",
     *     tags={"Classifications TNM"},
     *     summary="Créer une classification TNM",
     *     description="Enregistre une nouvelle classification TNM pour un patient",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TNMClassificationRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Classification TNM créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Classification TNM créée avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/TNMClassification")
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
            't_stage' => 'required|string',
            'n_stage' => 'required|string',
            'm_stage' => 'required|string',
            'overall_stage' => 'required|string',
            'grade' => 'required|string',
            'notes' => 'nullable|string',
            'classification_date' => 'required|date',
        ]);

        $tnmClassification = TNMClassification::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Classification TNM créée avec succès',
            'data' => $tnmClassification
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/tnm-classifications/{id}",
     *     operationId="getTNMClassificationById",
     *     tags={"Classifications TNM"},
     *     summary="Détails d'une classification TNM",
     *     description="Récupère les informations détaillées d'une classification TNM",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la classification TNM",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TNMClassification")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Classification TNM non trouvée")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $tnmClassification = TNMClassification::with('patient')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $tnmClassification
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tnm-classifications/patient/{patientId}",
     *     operationId="getTNMClassificationByPatient",
     *     tags={"Classifications TNM"},
     *     summary="Dernière classification TNM d'un patient",
     *     description="Récupère la dernière classification TNM enregistrée pour un patient spécifique",
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
     *             @OA\Property(property="data", ref="#/components/schemas/TNMClassification")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucune classification TNM trouvée pour ce patient",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucune classification TNM trouvée pour ce patient")
     *         )
     *     )
     * )
     */
    public function getByPatient(int $patientId): JsonResponse
    {
        $tnmClassification = TNMClassification::where('patient_id', $patientId)->latest()->first();

        if (!$tnmClassification) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune classification TNM  trouvée pour ce patient'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tnmClassification
        ]);
    }

    /**
     * @OA\Put(
     *     path="/tnm-classifications/{id}",
     *     operationId="updateTNMClassification",
     *     tags={"Classifications TNM"},
     *     summary="Mettre à jour une classification TNM",
     *     description="Met à jour les informations d'une classification TNM existante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la classification TNM",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/TNMClassificationRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Classification TNM mise à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Classification TNM mise à jour avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/TNMClassification")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Classification TNM non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tnmClassification = TNMClassification::findOrFail($id);

        // Validation des donnu00e9es
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            't_stage' => 'sometimes|string',
            'n_stage' => 'sometimes|string',
            'm_stage' => 'sometimes|string',
            'overall_stage' => 'sometimes|string',
            'grade' => 'sometimes|string',
            'notes' => 'nullable|string',
            'classification_date' => 'sometimes|date',
        ]);

        $tnmClassification->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Classification TNM mise à jour avec succès',
            'data' => $tnmClassification
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/tnm-classifications/{id}",
     *     operationId="deleteTNMClassification",
     *     tags={"Classifications TNM"},
     *     summary="Supprimer une classification TNM",
     *     description="Supprime une classification TNM du système",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la classification TNM à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Classification TNM supprimée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Classification TNM supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Classification TNM non trouvée")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $tnmClassification = TNMClassification::findOrFail($id);
        $tnmClassification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Classification TNM supprimé avec succès'
        ]);
    }
}
