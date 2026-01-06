<?php

declare(strict_types=1);

namespace Infrastructure\Build;

final class JsBuilder
{
    /**
     * @param array<string> $sourceFiles Array of source file paths (absolute or relative)
     * @param string $outputDir Directory where output files will be written
     * @param string $outputFile Name of development output file (e.g., 'app.js')
     * @param string $outputMinFile Name of production output file (e.g., 'app.min.js')
     */
    public function __construct(
        private readonly array $sourceFiles,
        private readonly string $outputDir,
        private readonly string $outputFile,
        private readonly string $outputMinFile
    ) {
    }

    public function build(bool $minify = false): void
    {
        $merged = '';

        foreach ($this->sourceFiles as $filePath) {
            if (!file_exists($filePath)) {
                throw new \RuntimeException("JavaScript file not found: {$filePath}");
            }

            $content = file_get_contents($filePath);
            $fileName = basename($filePath);
            $merged .= "/* ============================================\n";
            $merged .= "   {$fileName}\n";
            $merged .= "   ============================================ */\n\n";
            $merged .= $content . "\n\n";
        }

        $outputFileName = $minify ? $this->outputMinFile : $this->outputFile;
        $outputPath = rtrim($this->outputDir, '/') . '/' . $outputFileName;

        if ($minify) {
            $merged = $this->minify($merged);
        }

        // Ensure output directory exists
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        file_put_contents($outputPath, $merged);
        echo "✓ Built {$outputFileName}\n";
    }

    private function minify(string $js): string
    {
        // Remove multi-line comments (/* ... */) but preserve string literals
        // This regex handles comments that don't contain */ inside strings
        /** @var string $js */
        $js = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $js);

        // Remove single-line comments (//) but preserve URLs and string literals
        // Match // that are not inside strings
        /** @var string $js */
        $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);

        // Remove leading/trailing whitespace from each line
        /** @var string $js */
        $js = preg_replace('/^\s+|\s+$/m', '', $js);

        // Collapse multiple whitespace to single space (but preserve newlines in some contexts)
        /** @var string $js */
        $js = preg_replace('/\s+/', ' ', $js);

        // Remove whitespace around operators and punctuation
        /** @var string $js */
        $js = preg_replace('/\s*([{}();,\[\]+\-*\/=<>!&|?:;])\s*/', '$1', $js);

        // Remove whitespace after keywords
        /** @var string $js */
        $js = preg_replace(
            '/\b(const|let|var|function|if|else|for|while|return|new|typeof|instanceof)\s+/',
            '$1 ',
            $js
        );

        // Remove leading/trailing whitespace
        return trim($js);
    }

    public function watch(): void
    {
        echo "👀 Watching JavaScript files...\n";
        echo "Press Ctrl+C to stop\n\n";

        $lastModified = [];

        /** @phpstan-ignore-next-line */
        while (true) {
            $changed = false;

            foreach ($this->sourceFiles as $filePath) {
                if (!file_exists($filePath)) {
                    continue;
                }

                $currentModified = filemtime($filePath);

                if (!isset($lastModified[$filePath]) || $lastModified[$filePath] !== $currentModified) {
                    $lastModified[$filePath] = $currentModified;
                    $changed = true;
                }
            }

            if ($changed) {
                $this->build(minify: false);
                $this->build(minify: true);
            }

            usleep(500000); // Check every 500ms
        }
    }
}
