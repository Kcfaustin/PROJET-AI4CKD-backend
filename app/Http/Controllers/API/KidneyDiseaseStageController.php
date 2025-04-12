<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KidneyDiseaseStage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KidneyDiseaseStageController extends Controller
{
    /**
     * Affiche la liste des stades de la maladie rénale chronique.
     *
     * @return JsonResponse
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
     * Enregistre un nouveau stade de maladie rénale.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Affiche les détails d'un stade de maladie rénale spécifique.
     *
     * @param int $id
     * @return JsonResponse
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
     * Met à jour les informations d'un stade de maladie rénale.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
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
     * Supprime un stade de maladie rénale.
     *
     * @param int $id
     * @return JsonResponse
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
     * Détermine le stade de la maladie rénale en fonction du DFG.
     *
     * @param Request $request
     * @return JsonResponse
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
