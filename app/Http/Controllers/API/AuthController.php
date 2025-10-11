<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

    /**
 * @OA\Tag(
 *     name="Authentification",
 *     description="Gestion de l'authentification et des sessions utilisateurs"
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="docteur@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 *
 * @OA\Schema(
 *     schema="LoginResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Dr. Martin"),
 *         @OA\Property(property="email", type="string", example="docteur@example.com"),
 *         @OA\Property(property="role", type="string", example="medecin"),
 *         @OA\Property(property="status", type="string", example="actif")
 *     ),
 *     @OA\Property(property="token", type="string", example="1|abcdef123456...")
 * )
 *
 * @OA\Schema(
 *     schema="ChangePasswordRequest",
 *     type="object",
 *     required={"current_password", "password", "password_confirmation"},
 *     @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
 *     @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
 * )
 *
 * @OA\Schema(
 *     schema="ResetPasswordRequest",
 *     type="object",
 *     required={"password"},
 *     @OA\Property(property="password", type="string", format="password", example="newpassword123")
 * )
 */
class AuthController extends Controller
{
/**
     * @OA\Post(
     *     path="/login",
     *     operationId="loginUser",
     *     tags={"Authentification"},
     *     summary="Connexion utilisateur",
     *     description="Authentifie un utilisateur avec email et mot de passe et retourne un token d'accès",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentification réussie",
     *         @OA\JsonContent(ref="#/components/schemas/LoginResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Compte suspendu ou désactivé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Votre compte est suspendu. Veuillez contacter l'administrateur.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Identifiants incorrects",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Les informations d'identification fournies sont incorrectes."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="Les informations d'identification fournies sont incorrectes.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'],
            ]);
        }

        // Vérifier si l'utilisateur est actif
        if ($user->status !== 'actif') {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est ' . ($user->status === 'suspendu' ? 'suspendu' : 'désactivé') . '. Veuillez contacter l\'administrateur.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     operationId="logoutUser",
     *     tags={"Authentification"},
     *     summary="Déconnexion utilisateur",
     *     description="Révoque le token d'accès actuel de l'utilisateur authentifié",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Déconnexion réussie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

     /**
     * @OA\Get(
     *     path="/user",
     *     operationId="getAuthenticatedUser",
     *     tags={"Authentification"},
     *     summary="Récupérer l'utilisateur authentifié",
     *     description="Retourne les informations de l'utilisateur actuellement connecté",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Données de l'utilisateur",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Dr. Martin"),
     *                 @OA\Property(property="email", type="string", example="docteur@example.com"),
     *                 @OA\Property(property="role", type="string", example="medecin"),
     *                 @OA\Property(property="status", type="string", example="actif"),
     *                 @OA\Property(property="specialization", type="string", nullable=true, example="Néphrologie"),
     *                 @OA\Property(property="phone", type="string", nullable=true, example="+33612345678")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/change-password",
     *     operationId="changePassword",
     *     tags={"Authentification"},
     *     summary="Changer son mot de passe",
     *     description="Permet à un utilisateur authentifié de changer son propre mot de passe",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChangePasswordRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe modifié avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mot de passe modifié avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Mot de passe actuel incorrect ou validation échouée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Le mot de passe actuel est incorrect."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="current_password",
     *                     type="array",
     *                     @OA\Items(type="string", example="Le mot de passe actuel est incorrect.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Le mot de passe actuel est incorrect.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/users/{id}/reset-password",
     *     operationId="resetUserPassword",
     *     tags={"Authentification"},
     *     summary="Réinitialiser le mot de passe d'un utilisateur (Admin)",
     *     description="Permet à un administrateur de réinitialiser le mot de passe d'un utilisateur",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe réinitialisé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mot de passe réinitialisé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Action non autorisée (rôle non admin)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à effectuer cette action.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function resetPassword(Request $request, int $id): JsonResponse
    {
        // Vérifier si l'utilisateur connecté est un administrateur
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à effectuer cette action.'
            ], 403);
        }

        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès'
        ]);
    }
}
