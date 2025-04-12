<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicalHistoryController extends Controller
{
    /**
     * Affiche la liste des antu00e9cu00e9dents mu00e9dicaux.
     *
     * @return JsonResponse
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
     * Enregistre un nouvel antu00e9cu00e9dent mu00e9dical.
     *
     * @param Request $request
     * @return JsonResponse
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
            'message' => 'Antu00e9cu00e9dent mu00e9dical cru00e9u00e9 avec succu00e8s',
            'data' => $medicalHistory
        ], 201);
    }

    /**
     * Affiche les du00e9tails d'un antu00e9cu00e9dent mu00e9dical spu00e9cifique.
     *
     * @param int $id
     * @return JsonResponse
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
     * Affiche les antu00e9cu00e9dents mu00e9dicaux d'un patient spu00e9cifique.
     *
     * @param int $patientId
     * @return JsonResponse
     */
    public function getByPatient(int $patientId): JsonResponse
    {
        $medicalHistory = MedicalHistory::where('patient_id', $patientId)->latest()->first();
        
        if (!$medicalHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun antu00e9cu00e9dent mu00e9dical trouvu00e9 pour ce patient'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $medicalHistory
        ]);
    }

    /**
     * Met u00e0 jour les informations d'un antu00e9cu00e9dent mu00e9dical.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
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
            'message' => 'Antu00e9cu00e9dent mu00e9dical mis u00e0 jour avec succu00e8s',
            'data' => $medicalHistory
        ]);
    }

    /**
     * Supprime un antu00e9cu00e9dent mu00e9dical.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $medicalHistory = MedicalHistory::findOrFail($id);
        $medicalHistory->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Antu00e9cu00e9dent mu00e9dical supprimu00e9 avec succu00e8s'
        ]);
    }
}
