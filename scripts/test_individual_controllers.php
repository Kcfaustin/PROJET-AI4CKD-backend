<?php
require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;

echo "Testing Patient Controller...\n";
$result = Generator::scan([__DIR__ . '/../app/Http/Controllers/API/PatientController.php']);
echo "Patient paths found: " . (is_array($result->paths) ? count($result->paths) : 'none') . "\n";

echo "Testing Appointment Controller...\n";
$result = Generator::scan([__DIR__ . '/../app/Http/Controllers/API/AppointmentController.php']);
echo "Appointment paths found: " . (is_array($result->paths) ? count($result->paths) : 'none') . "\n";

echo "Testing Auth Controller...\n";
$result = Generator::scan([__DIR__ . '/../app/Http/Controllers/API/AuthController.php']);
echo "Auth paths found: " . (is_array($result->paths) ? count($result->paths) : 'none') . "\n";
