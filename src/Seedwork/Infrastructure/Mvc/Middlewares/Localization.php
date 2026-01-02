<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestContextKeys;
use Seedwork\Infrastructure\Mvc\Responses\Headers\ContentLanguage;
use Seedwork\Infrastructure\Mvc\Responses\Headers\Location;
use Seedwork\Infrastructure\Mvc\Responses\Headers\SetCookie;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Settings;

final class Localization extends Middleware
{
    public function __construct(
        private readonly Settings $settings,
        private readonly ResponseFactoryInterface $responseFactory,
        ?Middleware $next = null,
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        $context = $request->getAttribute(RequestContext::class);
        if (!$context instanceof RequestContext) {
            throw new \RuntimeException('RequestContext not found in request attributes');
        }

        if ($this->isSetLanguageCookieRequest($request)) {
            return $this->createSetLanguageResponse($request);
        }

        if ($this->hasValidLanguageCookie($request)) {
            return $this->handleRequestWithCurrentLanguage($this->next, $context, $request);
        }

        return $this->handleRequestWithAcceptedOrDefaultLanguage($this->next, $context, $request);
    }

    private function isSetLanguageCookieRequest(ServerRequestInterface $request): bool
    {
        $uri = $request->getUri()->getPath();
        $method = strtoupper($request->getMethod());
        return $method === 'POST' && $uri === $this->settings->languageSetUrl;
    }

    private function createSetLanguageResponse(ServerRequestInterface $request): ResponseInterface
    {
        $language = $this->getLanguageFromBodyOrDefault($request->getParsedBody());
        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->settings->languageCookieName,
            cookieValue: $language,
            expires: -1
        );
        $locationHeader = Location::redirectToInternal($request->getHeaderLine('Referer') ?: '/');
        $contentLanguageHeader = ContentLanguage::createFromCurrentLanguage($language);
        return $this->responseFactory
            ->createResponse(StatusCode::Found->value)
            ->withHeader($locationHeader->name, $locationHeader->value)
            ->withAddedHeader($contentLanguageHeader->name, $contentLanguageHeader->value)
            ->withAddedHeader($setCookieHeader->name, $setCookieHeader->value);
    }

    /**
     * @param array<string|mixed>|null|object $parsedBody
     */
    private function getLanguageFromBodyOrDefault(null|array|object $parsedBody): string
    {
        $language = is_array($parsedBody) && isset($parsedBody['language']) && is_string($parsedBody['language'])
            ? (string)$parsedBody['language']
            : null;

        return isset($language) && $this->isValidLanguage($language)
            ? $language
            : $this->settings->languageDefaultValue;
    }

    private function isValidLanguage(?string $language): bool
    {
        return isset($language) && in_array($language, $this->settings->languages, true);
    }

    private function hasValidLanguageCookie(ServerRequestInterface $request): bool
    {
        $cookieParams = $request->getCookieParams();
        /** @var string|null $cookieValue */
        $cookieValue = $cookieParams[$this->settings->languageCookieName] ?? null;

        return $this->isValidLanguage($cookieValue);
    }

    private function handleRequestWithCurrentLanguage(
        Middleware $next,
        RequestContext $context,
        ServerRequestInterface $request
    ): ResponseInterface {
        $cookieParams = $request->getCookieParams();
        /** @var string $language */
        $language = $cookieParams[$this->settings->languageCookieName];
        $context->set(RequestContextKeys::Language->value, $language);
        $contentLanguageHeader = ContentLanguage::createFromCurrentLanguage($language);
        return $next->handleRequest($request)
            ->withHeader($contentLanguageHeader->name, $contentLanguageHeader->value);
    }

    private function handleRequestWithAcceptedOrDefaultLanguage(
        Middleware $next,
        RequestContext $context,
        ServerRequestInterface $request
    ): ResponseInterface {
        $language = $this->getLanguageFromRequestOrDefault($request);
        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->settings->languageCookieName,
            cookieValue: $language,
            expires: -1
        );
        $contentLanguageHeader = ContentLanguage::createFromCurrentLanguage($language);
        $context->set(RequestContextKeys::Language->value, $language);
        return $next->handleRequest($request)
            ->withAddedHeader($contentLanguageHeader->name, $contentLanguageHeader->value)
            ->withAddedHeader($setCookieHeader->name, $setCookieHeader->value);
    }

    private function getLanguageFromRequestOrDefault(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine('Accept-Language');
        if (!$header) {
            return $this->settings->languageDefaultValue;
        }

        $header = preg_replace('/^Accept-Language:\s*/i', '', $header);
        $parts = $header !== null ? explode(',', $header) : [];

        $languages = [];
        foreach ($parts as $part) {
            // Extract language code before any ';'
            $lang = trim(explode(';', $part)[0]);
            if ($lang !== '') {
                $languages[] = $lang;
            }
        }

        $filtered = array_values(array_intersect($languages, $this->settings->languages));
        return $filtered[0] ?? $this->settings->languageDefaultValue;
    }
}
