<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalInfo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicalInfoController extends Controller
{
    /**
     * Affiche la liste des informations mu00e9dicales.
     *
     * @return JsonResponse
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
     * Enregistre une nouvelle information mu00e9dicale.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Affiche les du00e9tails d'une information mu00e9dicale spu00e9cifique.
     *
     * @param int $id
     * @return JsonResponse
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
     * Affiche les informations mu00e9dicales d'un patient spu00e9cifique.
     *
     * @param int $patientId
     * @return JsonResponse
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
                'message' => 'Aucune information mu00e9dicale trouvée pour ce patient'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $medicalInfo
        ]);
    }

    /**
     * Met u00e0 jour les informations d'une information mu00e9dicale.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
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
     * Supprime une information mu00e9dicale.
     *
     * @param int $id
     * @return JsonResponse
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
