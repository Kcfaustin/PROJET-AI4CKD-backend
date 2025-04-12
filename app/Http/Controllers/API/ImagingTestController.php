<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ImagingTest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ImagingTestController extends Controller
{
    /**
     * Affiche la liste des examens d'imagerie.
     *
     * @return JsonResponse
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
     * Enregistre un nouvel examen d'imagerie.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Affiche les du00e9tails d'un examen d'imagerie spu00e9cifique.
     *
     * @param int $id
     * @return JsonResponse
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
     * Affiche les examens d'imagerie d'un patient spu00e9cifique.
     *
     * @param int $patientId
     * @return JsonResponse
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
     * Met u00e0 jour les informations d'un examen d'imagerie.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
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
     * Supprime un examen d'imagerie.
     *
     * @param int $id
     * @return JsonResponse
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
