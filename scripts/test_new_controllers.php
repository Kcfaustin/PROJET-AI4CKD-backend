<?php
require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;

echo "Testing all controllers:\n";

$controllers = [
    'MedicalHistoryController',
    'MedicalInfoController',
    'PatientRecordController',
    'TNMClassificationController',
    'PatientController'
];

foreach ($controllers as $controller) {
    $path = __DIR__ . "/../app/Http/Controllers/API/{$controller}.php";
    if (file_exists($path)) {
        echo "✓ $controller exists\n";
        try {
            $result = Generator::scan([$path]);
            $pathCount = is_array($result->paths) ? count($result->paths) : 0;
            echo "  Paths found: $pathCount\n";
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✗ $controller missing\n";
    }
}
