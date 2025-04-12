<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Affiche la liste des patients.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $patients = Patient::with('referringDoctor')->get();
        
        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    /**
     * Enregistre un nouveau patient.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données
        $validated = $request->validate([
            'identifiant' => 'required|string|max:255|unique:patients',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|string|in:male,female',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'referring_doctor_id' => 'nullable|exists:users,id',
            'photo_url' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Patient créé avec succès',
            'data' => $patient
        ], 201);
    }

    /**
     * Affiche les détails d'un patient spécifique.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $patient = Patient::with('referringDoctor')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $patient
        ]);
    }

    /**
     * Met à jour les informations d'un patient.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);
        
        // Validation des données
        $validated = $request->validate([
            'identifiant' => 'sometimes|string|max:255|unique:patients,identifiant,' . $id,
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'birth_date' => 'sometimes|date',
            'gender' => 'sometimes|string|in:male,female',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'referring_doctor_id' => 'nullable|exists:users,id',
            'photo_url' => 'nullable|string',
        ]);

        $patient->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Patient mis à jour avec succès',
            'data' => $patient
        ]);
    }

    /**
     * Supprime un patient.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Patient supprimé avec succès'
        ]);
    }
}
