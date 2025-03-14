<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class CacheControl extends Header
{
    public function __construct(
        private bool $noCache = false,
        private bool $noStore = false,
        private ?int $maxAge = null,
        private ?int $sMaxAge = null,
        private bool $public = true,
        private bool $private = false,
        private bool $mustRevalidate = false,
        private bool $proxyRevalidate = false,
        private bool $noTransform = false,
        private bool $immutable = false,
        private ?int $staleWhileRevalidate = null,
        private ?int $staleIfError = null,
    ) {
        parent::__construct('Cache-Control', $this->buildValue());
    }

    private function buildValue(): string
    {
        $directives = [];

        if ($this->noCache) {
            $directives[] = 'no-cache';
        }

        if ($this->noStore) {
            $directives[] = 'no-store';
        }

        if ($this->maxAge !== null) {
            $directives[] = 'max-age=' . $this->maxAge;
        }

        if ($this->sMaxAge !== null) {
            $directives[] = 's-maxage=' . $this->sMaxAge;
        }

        if ($this->public) {
            $directives[] = 'public';
        }

        if ($this->private) {
            $directives[] = 'private';
        }

        if ($this->mustRevalidate) {
            $directives[] = 'must-revalidate';
        }

        if ($this->proxyRevalidate) {
            $directives[] = 'proxy-revalidate';
        }

        if ($this->noTransform) {
            $directives[] = 'no-transform';
        }

        if ($this->immutable) {
            $directives[] = 'immutable';
        }

        if ($this->staleWhileRevalidate !== null) {
            $directives[] = 'stale-while-revalidate=' . $this->staleWhileRevalidate;
        }

        if ($this->staleIfError !== null) {
            $directives[] = 'stale-if-error=' . $this->staleIfError;
        }

        return implode(', ', $directives);
    }
}
