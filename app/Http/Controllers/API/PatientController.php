<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Patients",
 *     description="Gestion des patients"
 * )
 */

class PatientController extends Controller
{
        /**
     * Liste tous les patients
     *
     * @OA\Get(
     *     path="/api/patients",
     *     tags={"Patients"},
     *     summary="Liste tous les patients",
     *     description="Récupère la liste complète des patients avec leurs médecins référents",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des patients récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="identifiant", type="string", example="PAT-001"),
     *                     @OA\Property(property="first_name", type="string", example="Jean"),
     *                     @OA\Property(property="last_name", type="string", example="Dupont"),
     *                     @OA\Property(property="birth_date", type="string", format="date", example="1990-05-15"),
     *                     @OA\Property(property="gender", type="string", example="male"),
     *                     @OA\Property(property="address", type="string", example="123 Rue de la Paix"),
     *                     @OA\Property(property="phone", type="string", example="+33612345678"),
     *                     @OA\Property(property="emergency_contact", type="string", example="Marie Dupont"),
     *                     @OA\Property(property="referring_doctor_id", type="integer", example=5),
     *                     @OA\Property(property="photo_url", type="string", example="https://example.com/photo.jpg")
     *                 )
     *             )
     *         )
     *     )
     * )
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
     * Créer un nouveau patient
     *
     * @OA\Post(
     *     path="/api/patients",
     *     tags={"Patients"},
     *     summary="Créer un nouveau patient",
     *     description="Enregistre un nouveau patient dans le système",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"identifiant", "first_name", "last_name", "birth_date", "gender"},
     *             @OA\Property(property="identifiant", type="string", example="PAT-002"),
     *             @OA\Property(property="first_name", type="string", example="Marie"),
     *             @OA\Property(property="last_name", type="string", example="Martin"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1985-08-20"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female"}, example="female"),
     *             @OA\Property(property="address", type="string", example="456 Avenue des Fleurs"),
     *             @OA\Property(property="phone", type="string", example="+33687654321"),
     *             @OA\Property(property="emergency_contact", type="string", example="Paul Martin"),
     *             @OA\Property(property="referring_doctor_id", type="integer", example=3),
     *             @OA\Property(property="photo_url", type="string", example="https://example.com/photo.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Patient créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Patient créé avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="identifiant", type="string", example="PAT-002"),
     *                 @OA\Property(property="first_name", type="string", example="Marie"),
     *                 @OA\Property(property="last_name", type="string", example="Martin")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
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
     * Afficher un patient spécifique
     *
     * @OA\Get(
     *     path="/api/patients/{id}",
     *     tags={"Patients"},
     *     summary="Afficher un patient",
     *     description="Récupère les informations détaillées d'un patient",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du patient",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du patient",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="identifiant", type="string", example="PAT-001"),
     *                 @OA\Property(property="first_name", type="string", example="Jean"),
     *                 @OA\Property(property="last_name", type="string", example="Dupont")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient non trouvé"
     *     )
     * )
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
     * Mettre à jour un patient
     *
     * @OA\Put(
     *     path="/api/patients/{id}",
     *     tags={"Patients"},
     *     summary="Mettre à jour un patient",
     *     description="Met à jour les informations d'un patient",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du patient",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="identifiant", type="string", example="PAT-001"),
     *             @OA\Property(property="first_name", type="string", example="Jean"),
     *             @OA\Property(property="last_name", type="string", example="Dupont"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-05-15"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *             @OA\Property(property="address", type="string", example="123 Rue de la Paix"),
     *             @OA\Property(property="phone", type="string", example="+33612345678"),
     *             @OA\Property(property="emergency_contact", type="string", example="Marie Dupont"),
     *             @OA\Property(property="referring_doctor_id", type="integer", example=5),
     *             @OA\Property(property="photo_url", type="string", example="https://example.com/photo.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Patient mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
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
     * Supprimer un patient
     *
     * @OA\Delete(
     *     path="/api/patients/{id}",
     *     tags={"Patients"},
     *     summary="Supprimer un patient",
     *     description="Supprime un patient du système",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du patient",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Patient supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient non trouvé"
     *     )
     * )
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
