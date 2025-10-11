<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API Gestion Médicale",
 *     version="1.0.0",
 *     description="Documentation de l'API de gestion médicale"
 * )
 * @OA\Server(
 *     url="https://hackathonbackend-73ba5772822d.herokuapp.com/api",
 *     description="Serveur de production"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Entrez votre token d'authentification (reçu lors du login)"
 * )
 */
abstract class Controller
{
    //
}
