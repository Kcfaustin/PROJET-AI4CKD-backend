<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TNMClassification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TNMClassificationController extends Controller
{
    /**
     * Affiche la liste des classifications TNM.
     *
     * @return JsonResponse
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
     * Enregistre une nouvelle classification TNM.
     *
     * @param Request $request
     * @return JsonResponse
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
            'message' => 'Classification TNM cru00e9u00e9e avec succu00e8s',
            'data' => $tnmClassification
        ], 201);
    }

    /**
     * Affiche les du00e9tails d'une classification TNM spu00e9cifique.
     *
     * @param int $id
     * @return JsonResponse
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
     * Affiche la classification TNM d'un patient spu00e9cifique.
     *
     * @param int $patientId
     * @return JsonResponse
     */
    public function getByPatient(int $patientId): JsonResponse
    {
        $tnmClassification = TNMClassification::where('patient_id', $patientId)->latest()->first();
        
        if (!$tnmClassification) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune classification TNM trouvu00e9e pour ce patient'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $tnmClassification
        ]);
    }

    /**
     * Met u00e0 jour les informations d'une classification TNM.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
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
            'message' => 'Classification TNM mise u00e0 jour avec succu00e8s',
            'data' => $tnmClassification
        ]);
    }

    /**
     * Supprime une classification TNM.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $tnmClassification = TNMClassification::findOrFail($id);
        $tnmClassification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Classification TNM supprimu00e9e avec succu00e8s'
        ]);
    }
}
