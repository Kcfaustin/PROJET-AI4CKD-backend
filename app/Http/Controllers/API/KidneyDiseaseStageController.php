<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KidneyDiseaseStage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Stades de maladie rénale",
 *     description="Gestion des stades de la maladie rénale chronique (CKD stages)"
 * )
 */

class KidneyDiseaseStageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/kidney-disease-stages",
     *     tags={"Stades de maladie rénale"},
     *     summary="Liste des stades de maladie rénale",
     *     description="Retourne la liste complète des stades de la maladie rénale chronique, ordonnés par numéro de stade",
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
     *                     @OA\Property(property="stage", type="integer", description="Numéro du stade (0-5)", example=1),
     *                     @OA\Property(property="name", type="string", description="Nom du stade", example="Stade 1 - Lésion rénale avec DFG normal"),
     *                     @OA\Property(property="description", type="string", description="Description détaillée", example="Lésion rénale avec DFG normal ou augmenté (≥90 mL/min/1,73m²). Généralement asymptomatique."),
     *                     @OA\Property(property="gfr_min", type="number", format="float", description="DFG minimum (mL/min/1,73m²)", example=90),
     *                     @OA\Property(property="gfr_max", type="number", format="float", description="DFG maximum (mL/min/1,73m²)", example=150),
     *                     @OA\Property(property="recommendations", type="string", description="Recommandations médicales", example="Suivi médical annuel, contrôle des facteurs de risque"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $stages = KidneyDiseaseStage::orderBy('stage', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $stages
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/kidney-disease-stages",
     *     tags={"Stades de maladie rénale"},
     *     summary="Créer un nouveau stade de maladie rénale",
     *     description="Enregistre un nouveau stade de maladie rénale chronique avec ses caractéristiques",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"stage", "name", "description", "gfr_min", "gfr_max"},
     *             @OA\Property(property="stage", type="integer", description="Numéro du stade (0-5, unique)", example=3, minimum=0, maximum=5),
     *             @OA\Property(property="name", type="string", description="Nom du stade", example="Stade 3 - Diminution modérée du DFG", maxLength=255),
     *             @OA\Property(property="description", type="string", description="Description détaillée du stade", example="Diminution modérée du DFG (30-59 mL/min/1,73m²). Complications possibles comme l'hypertension, l'anémie."),
     *             @OA\Property(property="gfr_min", type="number", format="float", description="Débit de filtration glomérulaire minimum en mL/min/1,73m²", example=30),
     *             @OA\Property(property="gfr_max", type="number", format="float", description="Débit de filtration glomérulaire maximum en mL/min/1,73m²", example=59),
     *             @OA\Property(property="recommendations", type="string", description="Recommandations médicales pour ce stade", example="Suivi médical trimestriel, consultation avec un néphrologue, régime alimentaire spécifique")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Stade créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stade de maladie rénale créé avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="stage", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="Stade 3 - Diminution modérée du DFG"),
     *                 @OA\Property(property="description", type="string", example="Diminution modérée du DFG"),
     *                 @OA\Property(property="gfr_min", type="number", example=30),
     *                 @OA\Property(property="gfr_max", type="number", example=59),
     *                 @OA\Property(property="recommendations", type="string", example="Suivi trimestriel"),
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
     *                     property="stage",
     *                     type="array",
     *                     @OA\Items(type="string", example="The stage has already been taken.")
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
            'stage' => 'required|integer|min:0|max:5|unique:kidney_disease_stages',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'gfr_min' => 'required|numeric',
            'gfr_max' => 'required|numeric',
            'recommendations' => 'nullable|string',
        ]);

        $stage = KidneyDiseaseStage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stade de maladie rénale créé avec succès',
            'data' => $stage
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/kidney-disease-stages/{id}",
     *     tags={"Stades de maladie rénale"},
     *     summary="Détails d'un stade de maladie rénale",
     *     description="Retourne les informations détaillées d'un stade spécifique de maladie rénale chronique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du stade",
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
     *                 @OA\Property(property="stage", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="Stade 3 - Diminution modérée du DFG"),
     *                 @OA\Property(property="description", type="string", example="Diminution modérée du DFG (30-59 mL/min/1,73m²)"),
     *                 @OA\Property(property="gfr_min", type="number", example=30),
     *                 @OA\Property(property="gfr_max", type="number", example=59),
     *                 @OA\Property(property="recommendations", type="string", example="Suivi médical trimestriel"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stade non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\KidneyDiseaseStage] 1")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $stage = KidneyDiseaseStage::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $stage
        ]);
    }

   /**
     * @OA\Put(
     *     path="/api/kidney-disease-stages/{id}",
     *     tags={"Stades de maladie rénale"},
     *     summary="Mettre à jour un stade de maladie rénale",
     *     description="Met à jour les informations d'un stade de maladie rénale chronique existant",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du stade",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="stage", type="integer", description="Numéro du stade (0-5)", example=3, minimum=0, maximum=5),
     *             @OA\Property(property="name", type="string", description="Nom du stade", example="Stade 3 - Diminution modérée du DFG", maxLength=255),
     *             @OA\Property(property="description", type="string", description="Description détaillée", example="Diminution modérée du DFG (30-59 mL/min/1,73m²). Complications possibles."),
     *             @OA\Property(property="gfr_min", type="number", format="float", description="DFG minimum", example=30),
     *             @OA\Property(property="gfr_max", type="number", format="float", description="DFG maximum", example=59),
     *             @OA\Property(property="recommendations", type="string", description="Recommandations médicales", example="Suivi médical trimestriel, consultation avec un néphrologue")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stade mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stade de maladie rénale mis à jour avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="stage", type="integer", example=3),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="gfr_min", type="number"),
     *                 @OA\Property(property="gfr_max", type="number"),
     *                 @OA\Property(property="recommendations", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stade non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $stage = KidneyDiseaseStage::findOrFail($id);

        // Validation des données
        $validated = $request->validate([
            'stage' => 'sometimes|integer|min:0|max:5|unique:kidney_disease_stages,stage,' . $id,
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'gfr_min' => 'sometimes|numeric',
            'gfr_max' => 'sometimes|numeric',
            'recommendations' => 'nullable|string',
        ]);

        $stage->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stade de maladie rénale mis à jour avec succès',
            'data' => $stage
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/kidney-disease-stages/{id}",
     *     tags={"Stades de maladie rénale"},
     *     summary="Supprimer un stade de maladie rénale",
     *     description="Supprime un stade de maladie rénale s'il n'est pas utilisé dans des dossiers patients",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du stade",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stade supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stade de maladie rénale supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Le stade est utilisé dans des dossiers patients",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ce stade de maladie rénale est utilisé dans des dossiers patients et ne peut pas être supprimé.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stade non trouvé"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $stage = KidneyDiseaseStage::findOrFail($id);

        // Vérifier si ce stade est utilisé dans des dossiers patients
        if ($stage->patientRecords()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ce stade de maladie rénale est utilisé dans des dossiers patients et ne peut pas être supprimé.'
            ], 400);
        }

        $stage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stade de maladie rénale supprimé avec succès'
        ]);
    }

   /**
     * @OA\Post(
     *     path="/api/kidney-disease-stages/determine-by-gfr",
     *     tags={"Stades de maladie rénale"},
     *     summary="Déterminer le stade par DFG",
     *     description="Détermine automatiquement le stade de la maladie rénale chronique en fonction du débit de filtration glomérulaire (DFG)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gfr"},
     *             @OA\Property(
     *                 property="gfr",
     *                 type="number",
     *                 format="float",
     *                 description="Débit de filtration glomérulaire en mL/min/1,73m²",
     *                 example=45.5,
     *                 minimum=0
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stade déterminé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="stage", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="Stade 3 - Diminution modérée du DFG"),
     *                 @OA\Property(property="description", type="string", example="Diminution modérée du DFG (30-59 mL/min/1,73m²)"),
     *                 @OA\Property(property="gfr_min", type="number", example=30),
     *                 @OA\Property(property="gfr_max", type="number", example=59),
     *                 @OA\Property(property="recommendations", type="string", example="Suivi médical trimestriel, consultation avec un néphrologue")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucun stade correspondant trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucun stade correspondant à ce DFG n'a été trouvé")
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
     *                     property="gfr",
     *                     type="array",
     *                     @OA\Items(type="string", example="The gfr field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function determineStageByGFR(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gfr' => 'required|numeric|min:0',
        ]);

        $gfr = $validated['gfr'];

        $stage = KidneyDiseaseStage::where('gfr_min', '<=', $gfr)
            ->where('gfr_max', '>=', $gfr)
            ->first();

        if (!$stage) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun stade correspondant à ce DFG n\'a été trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stage
        ]);
    }
}
