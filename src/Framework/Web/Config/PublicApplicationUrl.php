<?php

declare(strict_types=1);

namespace Framework\Config;

/**
 * Absolute public origin for building Location headers (e.g. local redirects). No trailing slash, no path.
 */
final readonly class PublicApplicationUrl
{
    private string $origin;

    public function __construct(string $origin)
    {
        $trimmed = rtrim(trim($origin), '/');
        if ($trimmed === '' || !preg_match('#\Ahttps?://#i', $trimmed)) {
            throw new \InvalidArgumentException(
                'Public application URL must be a non-empty absolute http(s) origin (e.g. https://app.example.com).'
            );
        }
        $parsed = parse_url($trimmed);
        if ($parsed === false || !isset($parsed['scheme'], $parsed['host'])) {
            throw new \InvalidArgumentException('Invalid public application URL.');
        }
        $path = $parsed['path'] ?? '';
        if ($path !== '' && $path !== '/') {
            throw new \InvalidArgumentException('Public application URL must not include a path; use origin only.');
        }
        $this->origin = $trimmed;
    }

    public function origin(): string
    {
        return $this->origin;
    }

    /**
     * @param string $absoluteOrRelativePath Path beginning with "/" or route-generated path string
     */
    public function absoluteUrlForPath(string $absoluteOrRelativePath): string
    {
        $path = $absoluteOrRelativePath;
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . $path;
        }

        return $this->origin . $path;
    }
}
