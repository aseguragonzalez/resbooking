<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Infrastructure\Build\JsBuilder;

// Dashboard-specific JavaScript configuration
$baseDir = __DIR__;
$scriptsDir = "{$baseDir}/assets/scripts";

$sourceFiles = [
    "{$scriptsDir}/main.js",
];

$outputDir = $scriptsDir;
$outputFile = 'app.js';
$outputMinFile = 'app.min.js';

// Create builder with configuration
$builder = new JsBuilder(
    sourceFiles: $sourceFiles,
    outputDir: $outputDir,
    outputFile: $outputFile,
    outputMinFile: $outputMinFile
);

// Handle CLI arguments
if (php_sapi_name() !== 'cli') {
    echo "This script can only be run from the command line.\n";
    exit(1);
}

if (isset($argv[1]) && $argv[1] === 'watch') {
    $builder->watch();
} else {
    $builder->build(minify: false);
    $builder->build(minify: true);
}
