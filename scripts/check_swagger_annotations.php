<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;
use OpenApi\Analysis;

try {
    echo "Scanning for annotations in Controllers...\n";

    $files = [
        __DIR__ . '/../app/Http/Controllers/API/AuthController.php',
        __DIR__ . '/../app/Http/Controllers/API/PatientController.php',
        __DIR__ . '/../app/Http/Controllers/API/AppointmentController.php',
        __DIR__ . '/../app/Http/Controllers/Controller.php'
    ];

    foreach ($files as $file) {
        echo "Checking file: $file\n";
        if (!file_exists($file)) {
            echo "  File not found!\n";
            continue;
        }

        try {
            $openapi = Generator::scan([$file]);
            $paths = $openapi->paths;
            if ($paths && is_array($paths) && count($paths) > 0) {
                echo "  Found " . count($paths) . " paths\n";
                foreach ($paths as $path) {
                    if (is_object($path) && isset($path->path)) {
                        echo "    Path: " . $path->path . "\n";
                    }
                }
            } else {
                echo "  No paths found (paths type: " . gettype($paths) . ")\n";
            }
        } catch (Exception $e) {
            echo "  Error scanning file: " . $e->getMessage() . "\n";
        }
    }

    // Full scan
    echo "\nFull scan:\n";
    $openapi = Generator::scan([
        __DIR__ . '/../app/Http/Controllers'
    ]);

    // Convert to JSON
    $json = $openapi->toJson(JSON_PRETTY_PRINT);

    echo "Generated OpenAPI spec:\n";
    echo $json;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
