<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response('<html><head><title>Laravel Backend API</title><style>body{font-family:Arial,sans-serif;margin:40px;background:#f8fafc;color:#333}</style></head><body><h1>🚀 Laravel Backend API</h1><p><strong>Status:</strong> ✅ Running</p><p><strong>Environment:</strong> Production</p><p><strong>API Endpoints:</strong> <a href="/api/users">/api/*</a></p><p><strong>Documentation:</strong> Available in DEPLOY_HEROKU.md</p><hr><p>Backend successfully deployed on Heroku!</p></body></html>', 200)->header('Content-Type', 'text/html');
});
