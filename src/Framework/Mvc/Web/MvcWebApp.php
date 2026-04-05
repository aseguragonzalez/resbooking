<?php

declare(strict_types=1);

namespace Framework\Mvc;

use Framework\Mvc\Middlewares\AllowedHttpMethodsForHtmlUi;
use Framework\Mvc\Middlewares\Authentication;
use Framework\Mvc\Middlewares\Authorization;
use Framework\Mvc\Middlewares\CsrfProtection;
use Framework\Mvc\Middlewares\ErrorHandling;
use Framework\Mvc\Middlewares\Localization;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Middlewares\RequestHandling;
use Framework\Mvc\Requests\RequestContext;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The base class for all MVC Web applications.
 *
 * The composition root must register {@see RequestContext::class} on the container for each HTTP request
 * before calling {@see handleRequest()} or {@see run()}.
 */
abstract class MvcWebApp extends WebApplication
{
    /**
     * @param ContainerInterface $container PSR-11 container.
     * @param string $basePath The base path of the application.
     * @param array<class-string<Middleware>> $middlewares The middlewares to use.
     * @param bool $requireAuthentication Whether to require authentication.
     * @param bool $requireRouteAccessControl Whether to enforce route authRequired/roles (requires authentication).
     * @param bool $enableCsrfProtection Whether to validate CSRF tokens on state-changing requests.
     */
    protected function __construct(
        ContainerInterface $container,
        string $basePath,
        private array $middlewares = [],
        private bool $requireAuthentication = false,
        private bool $requireRouteAccessControl = false,
        private bool $enableCsrfProtection = false,
    ) {
        parent::__construct($container, $basePath);
    }

    public function run(ServerRequestInterface $request): int
    {
        $this->emitResponse($this->handleRequest($request));

        return 0;
    }

    /**
     * Builds the middleware chain, dispatches the request, and returns the PSR-7 response (no SAPI output).
     *
     * Use this in integration tests; use {@see emitResponse()} or {@see run()} to send the response to PHP.
     */
    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RequestContext $context */
        $context = $this->container->get(RequestContext::class);
        $request = $request->withAttribute(RequestContext::class, $context);
        $chain = $this->buildMiddlewareChain();

        return $chain->handleRequest($request);
    }

    /**
     * Sends status, headers, and body to PHP's global HTTP response.
     */
    public function emitResponse(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        echo $response->getBody();
    }

    /**
     * @param class-string<Middleware> $middleware
     */
    public function addMiddleware(string $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Require authentication for the application.
     */
    public function useAuthentication(): void
    {
        $this->requireAuthentication = true;
    }

    /**
     * Enforce route access control (authRequired and roles on routes). Requires authentication to be enabled.
     *
     * @throws \LogicException When {@see useAuthentication()} was not called first.
     */
    public function useRouteAccessControl(): void
    {
        if (!$this->requireAuthentication) {
            throw new \LogicException(
                'Route access control requires authentication: call useAuthentication() before useRouteAccessControl().'
            );
        }
        $this->requireRouteAccessControl = true;
    }

    /**
     * Enable CSRF protection for state-changing HTTP methods.
     */
    public function useCsrfProtection(): void
    {
        $this->enableCsrfProtection = true;
    }

    private function buildMiddlewareChain(): Middleware
    {
        /** @var RequestHandling $lastMiddleware */
        $lastMiddleware = $this->container->get(RequestHandling::class);
        // Configure dynamic middlewares
        $middlewares = array_reverse($this->middlewares);
        $lastMiddleware = array_reduce($middlewares, function (Middleware $lastMiddleware, string $middleware) {
            /** @var Middleware $currentMiddleware */
            $currentMiddleware = $this->container->get($middleware);
            $currentMiddleware->setNext($lastMiddleware);

            return $currentMiddleware;
        }, $lastMiddleware);

        // Configure fixed middlewares: Route access control, Authentication, Localization, ErrorHandling
        if ($this->requireRouteAccessControl && $this->requireAuthentication) {
            /** @var Authorization $authorizationMiddleware */
            $authorizationMiddleware = $this->container->get(Authorization::class);
            $authorizationMiddleware->setNext($lastMiddleware);
            $lastMiddleware = $authorizationMiddleware;
        }

        if ($this->requireAuthentication) {
            /** @var Authentication $authenticationMiddleware */
            $authenticationMiddleware = $this->container->get(Authentication::class);
            $authenticationMiddleware->setNext($lastMiddleware);
            $lastMiddleware = $authenticationMiddleware;
        }

        if ($this->enableCsrfProtection) {
            /** @var CsrfProtection $csrfMiddleware */
            $csrfMiddleware = $this->container->get(CsrfProtection::class);
            $csrfMiddleware->setNext($lastMiddleware);
            $lastMiddleware = $csrfMiddleware;
        }

        /** @var Localization $localizationMiddleware */
        $localizationMiddleware = $this->container->get(Localization::class);
        $localizationMiddleware->setNext($lastMiddleware);
        $lastMiddleware = $localizationMiddleware;

        /** @var AllowedHttpMethodsForHtmlUi $allowedHttpMethodsMiddleware */
        $allowedHttpMethodsMiddleware = $this->container->get(AllowedHttpMethodsForHtmlUi::class);
        $allowedHttpMethodsMiddleware->setNext($lastMiddleware);
        $lastMiddleware = $allowedHttpMethodsMiddleware;

        /** @var ErrorHandling $errorMiddleware */
        $errorMiddleware = $this->container->get(ErrorHandling::class);
        $errorMiddleware->setNext($lastMiddleware);

        return $errorMiddleware;
    }
}
