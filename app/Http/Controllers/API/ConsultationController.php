<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConsultationController extends Controller
{
    /**
     * Affiche la liste des consultations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Consultation::with(['patient', 'doctor', 'patientRecord']);
        
        // Filtrer par médecin si spécifié
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filtrer par patient si spécifié
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        // Filtrer par date si spécifiée
        if ($request->has('date')) {
            $date = $request->date;
            $query->whereDate('consultation_date', $date);
        }
        
        $consultations = $query->orderBy('consultation_date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }

    /**
     * Enregistre une nouvelle consultation.
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
            'consultation_date' => 'required|date',
            'reason' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
            'decision' => 'nullable|string',
            'patient_record_id' => 'nullable|exists:patient_records,id',
        ]);

        $consultation = Consultation::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Consultation créée avec succès',
            'data' => $consultation
        ], 201);
    }

    /**
     * Affiche les détails d'une consultation spécifique.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $consultation = Consultation::with(['patient', 'doctor', 'patientRecord'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $consultation
        ]);
    }

    /**
     * Met à jour les informations d'une consultation.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $consultation = Consultation::findOrFail($id);
        
        // Validation des données
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'user_id' => 'sometimes|exists:users,id',
            'consultation_date' => 'sometimes|date',
            'reason' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
            'decision' => 'nullable|string',
            'patient_record_id' => 'nullable|exists:patient_records,id',
        ]);

        $consultation->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Consultation mise à jour avec succès',
            'data' => $consultation
        ]);
    }

    /**
     * Supprime une consultation.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Consultation supprimée avec succès'
        ]);
    }

    /**
     * Récupère l'historique des consultations d'un patient.
     *
     * @param int $patientId
     * @return JsonResponse
     */
    public function getPatientHistory(int $patientId): JsonResponse
    {
        $patient = Patient::findOrFail($patientId);
        
        $consultations = Consultation::where('patient_id', $patientId)
            ->with(['doctor', 'patientRecord'])
            ->orderBy('consultation_date', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => [
                'patient' => $patient,
                'consultations' => $consultations
            ]
        ]);
    }

    /**
     * Retourne les statistiques des consultations.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        $totalConsultations = Consultation::count();
        
        // Consultations par médecin
        $consultationsByDoctor = Consultation::selectRaw('user_id, count(*) as count')
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                $doctor = User::find($item->user_id);
                return [
                    'doctor_id' => $item->user_id,
                    'doctor_name' => $doctor ? $doctor->first_name . ' ' . $doctor->last_name : 'Inconnu',
                    'count' => $item->count
                ];
            });
        
        // Consultations par jour (30 derniers jours)
        $consultationsByDay = Consultation::selectRaw('DATE(consultation_date) as date, count(*) as count')
            ->whereDate('consultation_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_consultations' => $totalConsultations,
                'consultations_by_doctor' => $consultationsByDoctor,
                'consultations_by_day' => $consultationsByDay
            ]
        ]);
    }
}
