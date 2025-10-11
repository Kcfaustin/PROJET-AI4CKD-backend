<?php
require __DIR__ . '/../vendor/autoload.php';
use OpenApi\Generator;
try {
    $file = __DIR__ . '/../app/Swagger/SwaggerAnnotations.php';
    echo "Checking file: $file\n";
    if (!file_exists($file)) {
        echo "File not found.\n";
        exit(1);
    }
    echo "--- File head (first 40 lines) ---\n";
    $lines = file($file);
    for ($i = 0; $i < min(40, count($lines)); $i++) {
        echo ($i+1) . ": " . rtrim($lines[$i]) . "\n";
    }

        $file = __DIR__ . '/../app/Swagger/OpenApiRoot.php';
        echo "Scanning file: $file\n";
        if (!file_exists($file)) {
            echo "File not found: $file\n";
            exit(1);
        }
        $openapi = Generator::scan([$file]);
        echo "Class: " . (is_object($openapi) ? get_class($openapi) : gettype($openapi)) . "\n";
        echo "Has info? ";
        echo isset($openapi->info) ? "yes\n" : "no\n";
        if (isset($openapi->info)) {
            echo "Info: \n";
            var_export($openapi->info);
            echo "\n";
        }
        echo "Has paths? ";
        echo isset($openapi->paths) ? "yes\n" : "no\n";
        if (isset($openapi->paths)) {
            echo "Paths: \n";
            var_export($openapi->paths);
            echo "\n";
        }
        // Show total annotations found via Analysis if available
        if (property_exists($openapi, '_analysis') && is_object($openapi->_analysis)) {
            $analysis = $openapi->_analysis;
            if (property_exists($analysis, 'annotations') && is_array($analysis->annotations)) {
                echo "Annotations count: " . count($analysis->annotations) . "\n";
            }
        }
} catch (\Throwable $e) {
    echo "Exception: " . get_class($e) . " - " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
