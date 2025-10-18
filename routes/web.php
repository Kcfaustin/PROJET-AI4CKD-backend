<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    $docUrl = url('/api/documentation');
    return response("
    <html>
        <head>
            <title>Laravel Backend API</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 40px;
                    background: #f8fafc;
                    color: #333;
                    text-align: center;
                }
                .container {
                    max-width: 600px;
                    margin: 100px auto;
                    padding: 40px;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                h1 { color: #2563eb; }
                .btn {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 12px 30px;
                    background: #2563eb;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                }
                .btn:hover { background: #1d4ed8; }
                .status { color: #10b981; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>🚀 API Gestion Médicale</h1>
                <p><strong>Status:</strong> <span class='status'>✅ Running</span></p>
                <p><strong>Environment:</strong> Production</p>
                <p><strong>Version:</strong> 1.0.0</p>
                <hr>
                <p>Backend déployé avec succès sur Heroku!</p>
                <a href='{$docUrl}' class='btn'>📚 Voir la Documentation API</a>
            </div>
        </body>
    </html>
    ", 200)->header('Content-Type', 'text/html');
});

// Route pour servir le fichier JSON de la documentation Swagger
Route::get('/docs/api-docs.json', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'API documentation not found'], 404);
    }
    
    return response()->file($filePath, [
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET',
        'Access-Control-Allow-Headers' => 'Content-Type',
    ]);
});

// Route pour gérer le format de requête avec paramètre de requête (comme généré par L5-Swagger)
Route::get('/docs', function (Illuminate\Http\Request $request) {
    $jsonFile = $request->query('api-docs.json');
    
    if ($jsonFile !== null) {
        $filePath = storage_path('api-docs/api-docs.json');
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'API documentation not found'], 404);
        }
        
        return response()->file($filePath, [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET',
            'Access-Control-Allow-Headers' => 'Content-Type',
        ]);
    }
    
    // Si pas de paramètre, rediriger vers la documentation Swagger
    return redirect('/api/documentation');
});
