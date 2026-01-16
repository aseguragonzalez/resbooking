<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Framework\Build\CssBuilder;

// Dashboard-specific CSS configuration
$baseDir = __DIR__;
$stylesDir = "{$baseDir}/assets/styles";

$sourceFiles = [
    "{$stylesDir}/root.css",
    "{$stylesDir}/layout.css",
    "{$stylesDir}/main.css",
];

$outputDir = $stylesDir;
$outputFile = 'app.css';
$outputMinFile = 'app.min.css';

// Create builder with configuration
$builder = new CssBuilder(
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
