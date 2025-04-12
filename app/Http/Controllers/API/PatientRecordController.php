<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PatientRecord;
use App\Models\Patient;
use App\Models\KidneyDiseaseStage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientRecordController extends Controller
{
    /**
     * Affiche la liste des dossiers médicaux.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Enregistre un nouveau dossier médical.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Affiche les détails d'un dossier médical spécifique.
     *
     * @param int $id
     * @return JsonResponse
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
     * Met à jour les informations d'un dossier médical.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
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
     * Supprime un dossier médical.
     *
     * @param int $id
     * @return JsonResponse
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
     * Récupère le dernier dossier médical d'un patient.
     *
     * @param int $patientId
     * @return JsonResponse
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
     * Retourne les statistiques des dossiers médicaux.
     *
     * @return JsonResponse
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
