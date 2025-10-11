<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ImagingTest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Examens d'imagerie",
 *     description="Gestion des examens d'imagerie médicale (échographie, scanner, IRM, etc.)"
 * )
 */

class ImagingTestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/imaging-tests",
     *     tags={"Examens d'imagerie"},
     *     summary="Liste des examens d'imagerie",
     *     description="Retourne la liste complète de tous les examens d'imagerie avec les informations des patients",
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="patient_id", type="integer", example=5),
     *                     @OA\Property(property="has_ultrasound", type="boolean", example=true),
     *                     @OA\Property(property="ultrasound_date", type="string", format="date", example="2025-10-15"),
     *                     @OA\Property(property="ultrasound_results", type="string", example="Résultats normaux"),
     *                     @OA\Property(property="has_ct_scan", type="boolean", example=false),
     *                     @OA\Property(property="ct_scan_date", type="string", format="date", example=null),
     *                     @OA\Property(property="ct_scan_results", type="string", example=null),
     *                     @OA\Property(property="has_mri", type="boolean", example=true),
     *                     @OA\Property(property="mri_date", type="string", format="date", example="2025-09-20"),
     *                     @OA\Property(property="mri_results", type="string", example="Anomalie détectée"),
     *                     @OA\Property(property="has_other_imaging", type="boolean", example=false),
     *                     @OA\Property(property="other_imaging_type", type="string", example=null),
     *                     @OA\Property(property="other_imaging_date", type="string", format="date", example=null),
     *                     @OA\Property(property="other_imaging_results", type="string", example=null),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(
     *                         property="patient",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="first_name", type="string", example="Jean"),
     *                         @OA\Property(property="last_name", type="string", example="Dupont"),
     *                         @OA\Property(property="email", type="string", example="jean.dupont@example.com")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $imagingTests = ImagingTest::with('patient')->get();

        return response()->json([
            'success' => true,
            'data' => $imagingTests
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/imaging-tests",
     *     tags={"Examens d'imagerie"},
     *     summary="Créer un nouvel examen d'imagerie",
     *     description="Enregistre un nouvel examen d'imagerie pour un patient",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_id"},
     *             @OA\Property(property="patient_id", type="integer", description="ID du patient", example=5),
     *             @OA\Property(property="has_ultrasound", type="boolean", description="A eu une échographie", example=true),
     *             @OA\Property(property="ultrasound_date", type="string", format="date", description="Date de l'échographie", example="2025-10-15"),
     *             @OA\Property(property="ultrasound_results", type="string", description="Résultats de l'échographie", example="Résultats normaux, aucune anomalie détectée"),
     *             @OA\Property(property="has_ct_scan", type="boolean", description="A eu un scanner (CT)", example=false),
     *             @OA\Property(property="ct_scan_date", type="string", format="date", description="Date du scanner", example=null),
     *             @OA\Property(property="ct_scan_results", type="string", description="Résultats du scanner", example=null),
     *             @OA\Property(property="has_mri", type="boolean", description="A eu une IRM", example=true),
     *             @OA\Property(property="mri_date", type="string", format="date", description="Date de l'IRM", example="2025-09-20"),
     *             @OA\Property(property="mri_results", type="string", description="Résultats de l'IRM", example="Anomalie détectée au niveau du rein gauche"),
     *             @OA\Property(property="has_other_imaging", type="boolean", description="A eu un autre type d'imagerie", example=false),
     *             @OA\Property(property="other_imaging_type", type="string", description="Type d'imagerie supplémentaire", example=null),
     *             @OA\Property(property="other_imaging_date", type="string", format="date", description="Date de l'imagerie supplémentaire", example=null),
     *             @OA\Property(property="other_imaging_results", type="string", description="Résultats de l'imagerie supplémentaire", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Examen d'imagerie créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Examen d'imagerie créé avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="has_ultrasound", type="boolean", example=true),
     *                 @OA\Property(property="ultrasound_date", type="string", format="date", example="2025-10-15"),
     *                 @OA\Property(property="ultrasound_results", type="string", example="Résultats normaux"),
     *                 @OA\Property(property="has_ct_scan", type="boolean", example=false),
     *                 @OA\Property(property="has_mri", type="boolean", example=true),
     *                 @OA\Property(property="mri_date", type="string", format="date", example="2025-09-20"),
     *                 @OA\Property(property="mri_results", type="string", example="Anomalie détectée"),
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
        // Validation des donnu00e9es
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'has_ultrasound' => 'boolean',
            'ultrasound_date' => 'nullable|date',
            'ultrasound_results' => 'nullable|string',
            'has_ct_scan' => 'boolean',
            'ct_scan_date' => 'nullable|date',
            'ct_scan_results' => 'nullable|string',
            'has_mri' => 'boolean',
            'mri_date' => 'nullable|date',
            'mri_results' => 'nullable|string',
            'has_other_imaging' => 'boolean',
            'other_imaging_type' => 'nullable|string',
            'other_imaging_date' => 'nullable|date',
            'other_imaging_results' => 'nullable|string',
        ]);

        $imagingTest = ImagingTest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Examen d\'imagerie cru00e9u00e9 avec succu00e8s',
            'data' => $imagingTest
        ], 201);
    }

      /**
     * @OA\Get(
     *     path="/api/imaging-tests/{id}",
     *     tags={"Examens d'imagerie"},
     *     summary="Détails d'un examen d'imagerie",
     *     description="Retourne les informations détaillées d'un examen d'imagerie spécifique avec les données du patient",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'examen d'imagerie",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="has_ultrasound", type="boolean", example=true),
     *                 @OA\Property(property="ultrasound_date", type="string", format="date", example="2025-10-15"),
     *                 @OA\Property(property="ultrasound_results", type="string", example="Résultats normaux"),
     *                 @OA\Property(property="has_ct_scan", type="boolean", example=false),
     *                 @OA\Property(property="ct_scan_date", type="string", format="date", example=null),
     *                 @OA\Property(property="ct_scan_results", type="string", example=null),
     *                 @OA\Property(property="has_mri", type="boolean", example=true),
     *                 @OA\Property(property="mri_date", type="string", format="date", example="2025-09-20"),
     *                 @OA\Property(property="mri_results", type="string", example="Anomalie détectée"),
     *                 @OA\Property(property="has_other_imaging", type="boolean", example=false),
     *                 @OA\Property(property="other_imaging_type", type="string", example=null),
     *                 @OA\Property(property="other_imaging_date", type="string", format="date", example=null),
     *                 @OA\Property(property="other_imaging_results", type="string", example=null),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="first_name", type="string", example="Jean"),
     *                     @OA\Property(property="last_name", type="string", example="Dupont"),
     *                     @OA\Property(property="email", type="string", example="jean.dupont@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+229123456789")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Examen d'imagerie non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\ImagingTest] 1")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $imagingTest = ImagingTest::with('patient')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $imagingTest
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/imaging-tests/patient/{patientId}",
     *     tags={"Examens d'imagerie"},
     *     summary="Dernier examen d'imagerie d'un patient",
     *     description="Récupère le dernier examen d'imagerie effectué pour un patient spécifique",
     *     @OA\Parameter(
     *         name="patientId",
     *         in="path",
     *         description="ID du patient",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Examen récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="has_ultrasound", type="boolean", example=true),
     *                 @OA\Property(property="ultrasound_date", type="string", format="date", example="2025-10-15"),
     *                 @OA\Property(property="ultrasound_results", type="string", example="Résultats normaux"),
     *                 @OA\Property(property="has_ct_scan", type="boolean", example=false),
     *                 @OA\Property(property="has_mri", type="boolean", example=true),
     *                 @OA\Property(property="mri_date", type="string", format="date", example="2025-09-20"),
     *                 @OA\Property(property="mri_results", type="string", example="Anomalie détectée"),
     *                 @OA\Property(property="has_other_imaging", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucun examen trouvé pour ce patient",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucun examen d'imagerie trouvé pour ce patient")
     *         )
     *     )
     * )
     */
    public function getByPatient(int $patientId): JsonResponse
    {
        $imagingTest = ImagingTest::where('patient_id', $patientId)->latest()->first();

        if (!$imagingTest) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun examen d\'imagerie trouvu00e9 pour ce patient'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $imagingTest
        ]);
    }

     /**
     * @OA\Put(
     *     path="/api/imaging-tests/{id}",
     *     tags={"Examens d'imagerie"},
     *     summary="Mettre à jour un examen d'imagerie",
     *     description="Met à jour les informations d'un examen d'imagerie existant",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'examen d'imagerie",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=5),
     *             @OA\Property(property="has_ultrasound", type="boolean", example=true),
     *             @OA\Property(property="ultrasound_date", type="string", format="date", example="2025-10-15"),
     *             @OA\Property(property="ultrasound_results", type="string", example="Résultats normaux mis à jour"),
     *             @OA\Property(property="has_ct_scan", type="boolean", example=true),
     *             @OA\Property(property="ct_scan_date", type="string", format="date", example="2025-10-20"),
     *             @OA\Property(property="ct_scan_results", type="string", example="Scanner effectué, résultats dans la norme"),
     *             @OA\Property(property="has_mri", type="boolean", example=true),
     *             @OA\Property(property="mri_date", type="string", format="date", example="2025-09-20"),
     *             @OA\Property(property="mri_results", type="string", example="Anomalie détectée"),
     *             @OA\Property(property="has_other_imaging", type="boolean", example=false),
     *             @OA\Property(property="other_imaging_type", type="string", example=null),
     *             @OA\Property(property="other_imaging_date", type="string", format="date", example=null),
     *             @OA\Property(property="other_imaging_results", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Examen d'imagerie mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Examen d'imagerie mis à jour avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="has_ultrasound", type="boolean", example=true),
     *                 @OA\Property(property="ultrasound_date", type="string", format="date"),
     *                 @OA\Property(property="ultrasound_results", type="string"),
     *                 @OA\Property(property="has_ct_scan", type="boolean"),
     *                 @OA\Property(property="has_mri", type="boolean"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Examen d'imagerie non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $imagingTest = ImagingTest::findOrFail($id);

        // Validation des donnu00e9es
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'has_ultrasound' => 'boolean',
            'ultrasound_date' => 'nullable|date',
            'ultrasound_results' => 'nullable|string',
            'has_ct_scan' => 'boolean',
            'ct_scan_date' => 'nullable|date',
            'ct_scan_results' => 'nullable|string',
            'has_mri' => 'boolean',
            'mri_date' => 'nullable|date',
            'mri_results' => 'nullable|string',
            'has_other_imaging' => 'boolean',
            'other_imaging_type' => 'nullable|string',
            'other_imaging_date' => 'nullable|date',
            'other_imaging_results' => 'nullable|string',
        ]);

        $imagingTest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Examen d\'imagerie mis u00e0 jour avec succu00e8s',
            'data' => $imagingTest
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/imaging-tests/{id}",
     *     tags={"Examens d'imagerie"},
     *     summary="Supprimer un examen d'imagerie",
     *     description="Supprime un examen d'imagerie de la base de données",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'examen d'imagerie",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Examen d'imagerie supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Examen d'imagerie supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Examen d'imagerie non trouvé"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $imagingTest = ImagingTest::findOrFail($id);
        $imagingTest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Examen d\'imagerie supprimu00e9 avec succu00e8s'
        ]);
    }
}
