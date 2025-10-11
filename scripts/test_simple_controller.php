<?php
require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;

echo "Testing Simple Patient Controller...\n";
$result = Generator::scan([__DIR__ . '/../app/Http/Controllers/API/PatientControllerTest.php']);
echo "Patient test paths found: " . (is_array($result->paths) ? count($result->paths) : 'none') . "\n";

if (is_array($result->paths) && count($result->paths) > 0) {
    foreach ($result->paths as $path) {
        if (is_object($path) && isset($path->path)) {
            echo "  Path found: " . $path->path . "\n";
        }
    }
}
