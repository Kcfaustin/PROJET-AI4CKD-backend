<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Médecins",
 *     description="Gestion des médecins"
 * )
 */

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Médecins"},
     *     summary="Liste des médecins",
     *     description="Retourne la liste de tous les médecins",
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
     *                     @OA\Property(property="first_name", type="string", example="Jean"),
     *                     @OA\Property(property="last_name", type="string", example="Dupont"),
     *                     @OA\Property(property="email", type="string", example="jean.dupont@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+229123456789"),
     *                     @OA\Property(property="license_number", type="string", example="MED123456"),
     *                     @OA\Property(property="specialization", type="string", example="Cardiologie"),
     *                     @OA\Property(property="role", type="string", example="medecin"),
     *                     @OA\Property(property="status", type="string", example="actif"),
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
        $users = User::where('role', 'medecin')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Médecins"},
     *     summary="Créer un nouveau médecin",
     *     description="Enregistre un nouveau médecin dans le système",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password", "role", "status"},
     *             @OA\Property(property="first_name", type="string", example="Jean"),
     *             @OA\Property(property="last_name", type="string", example="Dupont"),
     *             @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
     *             @OA\Property(property="phone", type="string", example="+229123456789"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", minLength=8),
     *             @OA\Property(property="license_number", type="string", example="MED123456"),
     *             @OA\Property(property="specialization", type="string", example="Cardiologie"),
     *             @OA\Property(property="role", type="string", enum={"medecin", "admin"}, example="medecin"),
     *             @OA\Property(property="status", type="string", enum={"actif", "suspendu", "desactive"}, example="actif")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Médecin créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Médecin créé avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="Jean"),
     *                 @OA\Property(property="last_name", type="string", example="Dupont"),
     *                 @OA\Property(property="email", type="string", example="jean.dupont@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+229123456789"),
     *                 @OA\Property(property="license_number", type="string", example="MED123456"),
     *                 @OA\Property(property="specialization", type="string", example="Cardiologie"),
     *                 @OA\Property(property="role", type="string", example="medecin"),
     *                 @OA\Property(property="status", type="string", example="actif"),
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
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email has already been taken.")
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'license_number' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'role' => 'required|string|in:medecin,admin',
            'status' => 'required|string|in:actif,suspendu,desactive',
        ]);

        // Hashage du mot de passe
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Médecin créé avec succès',
            'data' => $user
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Médecins"},
     *     summary="Détails d'un médecin",
     *     description="Retourne les informations détaillées d'un médecin avec ses patients",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du médecin",
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
     *                 @OA\Property(property="first_name", type="string", example="Jean"),
     *                 @OA\Property(property="last_name", type="string", example="Dupont"),
     *                 @OA\Property(property="email", type="string", example="jean.dupont@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+229123456789"),
     *                 @OA\Property(property="license_number", type="string", example="MED123456"),
     *                 @OA\Property(property="specialization", type="string", example="Cardiologie"),
     *                 @OA\Property(property="role", type="string", example="medecin"),
     *                 @OA\Property(property="status", type="string", example="actif"),
     *                 @OA\Property(
     *                     property="patients",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="first_name", type="string", example="Marie"),
     *                         @OA\Property(property="last_name", type="string", example="Martin")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Médecin non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\User] 1")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with('patients')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

   /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Médecins"},
     *     summary="Mettre à jour un médecin",
     *     description="Met à jour les informations d'un médecin existant",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du médecin",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="Jean"),
     *             @OA\Property(property="last_name", type="string", example="Dupont"),
     *             @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
     *             @OA\Property(property="phone", type="string", example="+229123456789"),
     *             @OA\Property(property="license_number", type="string", example="MED123456"),
     *             @OA\Property(property="specialization", type="string", example="Cardiologie"),
     *             @OA\Property(property="status", type="string", enum={"actif", "suspendu", "desactive"}, example="actif"),
     *             @OA\Property(property="role", type="string", enum={"medecin", "admin"}, example="medecin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Médecin mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Médecin mis à jour avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="Jean"),
     *                 @OA\Property(property="last_name", type="string", example="Dupont")
     *             ),
     *             @OA\Property(
     *                 property="changes",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", example="actif")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Aucune donnée valide fournie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucune donnée valide fournie"),
     *             @OA\Property(property="received_data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Médecin non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */

public function update(Request $request, int $id): JsonResponse
{
    $user = User::findOrFail($id);

    \Log::info('Données brutes reçues:', $request->all());

    // Validation modifiée (enlever 'sometimes')
    $validated = $request->validate([
        'first_name' => 'string|max:255',
        'last_name' => 'string|max:255',
        'email' => 'email|unique:users,email,'.$id,
        'phone' => 'nullable|string|max:20',
        'license_number' => 'nullable|string|max:255',
        'specialization' => 'nullable|string|max:255',
        'status' => 'string|in:actif,suspendu,desactive',
        'role' => 'string|in:medecin,admin'
    ]);

    \Log::info('Données validées:', $validated);

    if (empty($validated)) {
        return response()->json([
            'success' => false,
            'message' => 'Aucune donnée valide fournie',
            'received_data' => $request->all() // Pour debug
        ], 400);
    }

    $user->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Médecin mis à jour avec succès',
        'data' => $user->fresh(),
        'changes' => $user->getChanges() // Montre les champs modifiés
    ]);
}

     /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Médecins"},
     *     summary="Supprimer un médecin",
     *     description="Supprime un médecin s'il n'a pas de patients assignés",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du médecin",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Médecin supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Médecin supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Le médecin a des patients assignés",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ce médecin a des patients assignés. Veuillez les réassigner avant de supprimer ce médecin.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Médecin non trouvé"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Vérifier si le médecin a des patients assignés
        if ($user->patients()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ce médecin a des patients assignés. Veuillez les réassigner avant de supprimer ce médecin.'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Médecin supprimé avec succès'
        ]);
    }

   /**
     * @OA\Get(
     *     path="/api/users/statistics",
     *     tags={"Médecins"},
     *     summary="Statistiques des médecins",
     *     description="Retourne des statistiques globales sur les médecins et patients",
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_doctors", type="integer", example=25),
     *                 @OA\Property(property="active_doctors", type="integer", example=20),
     *                 @OA\Property(property="desactive_doctors", type="integer", example=3),
     *                 @OA\Property(property="suspendu_doctors", type="integer", example=2),
     *                 @OA\Property(property="total_patients", type="integer", example=150),
     *                 @OA\Property(property="average_patients_per_doctor", type="number", format="float", example=6.0)
     *             )
     *         )
     *     )
     * )
     */
    public function statistics(): JsonResponse
    {
        $totalDoctors = User::where('role', 'medecin')->count();
        $activeDoctors = User::where('role', 'medecin')->where('status', 'actif')->count();
        $desactiveDoctors = User::where('role', 'medecin')->where('status', 'desactive')->count();
        $suspenduDoctors = User::where('role', 'medecin')->where('status', 'suspendu')->count();

        // Calculer le nombre total de patients
        $totalPatients = \App\Models\Patient::count();

        // Calculer le nombre moyen de patients par médecin
        $averagePatientsPerDoctor = $totalDoctors > 0 ? $totalPatients / $totalDoctors : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_doctors' => $totalDoctors,
                'active_doctors' => $activeDoctors,
                'desactive_doctors' => $desactiveDoctors,
                'suspendu_doctors' => $suspenduDoctors,
                'total_patients' => $totalPatients,
                'average_patients_per_doctor' => round($averagePatientsPerDoctor, 2)
            ]
        ]);
    }
}
