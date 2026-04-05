<?php

declare(strict_types=1);

namespace Framework;

use Framework\Middlewares\AllowedHttpMethodsForHtmlUi;
use Framework\Middlewares\Authentication;
use Framework\Middlewares\Authorization;
use Framework\Middlewares\CsrfProtection;
use Framework\Middlewares\ErrorHandling;
use Framework\Middlewares\Localization;
use Framework\Middlewares\Middleware;
use Framework\Middlewares\RequestHandling;
use Framework\Requests\RequestContext;
use Framework\Views\ViewEngine;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

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
        private ?ErrorSettings $errorSettings = null,
    ) {
        parent::__construct($container, $basePath);
    }

    /**
     * Handles the request, emits the HTTP response, and returns a process exit code.
     *
     * Exit codes for uncaught errors (exceptions that escape {@see handleRequest()} or {@see emitResponse()}):
     * 0 — success; 1 — other {@see \Throwable}; 2 — {@see NotFoundExceptionInterface} (container binding);
     * 3 — {@see \InvalidArgumentException}; 4 — {@see \LogicException}; 5 — {@see \RuntimeException}.
     *
     * Errors converted to HTTP responses by {@see ErrorHandling} still yield 0.
     */
    public function run(ServerRequestInterface $request): int
    {
        try {
            $this->emitResponse($this->handleRequest($request));

            return 0;
        } catch (\Throwable $e) {
            return $this->exitCodeForThrowable($e);
        }
    }

    private function exitCodeForThrowable(\Throwable $e): int
    {
        if ($e instanceof NotFoundExceptionInterface) {
            return 2;
        }
        if ($e instanceof \InvalidArgumentException) {
            return 3;
        }
        if ($e instanceof \LogicException) {
            return 4;
        }
        if ($e instanceof \RuntimeException) {
            return 5;
        }

        return 1;
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

    /**
     * Maps exception types to HTTP status and error views for {@see ErrorHandling}.
     * Call from the HTTP entrypoint before {@see run()} or {@see handleRequest()}.
     * If omitted, {@see ErrorSettings::frameworkDefault()} is used.
     */
    public function useErrorSettings(ErrorSettings $settings): void
    {
        $this->errorSettings = $settings;
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

        $effectiveErrorSettings = $this->errorSettings ?? ErrorSettings::frameworkDefault();

        /** @var LoggerInterface $logger */
        $logger = $this->container->get(LoggerInterface::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->container->get(ResponseFactoryInterface::class);
        /** @var ViewEngine $viewEngine */
        $viewEngine = $this->container->get(ViewEngine::class);

        $errorMiddleware = new ErrorHandling(
            settings: $effectiveErrorSettings,
            logger: $logger,
            responseFactory: $responseFactory,
            viewEngine: $viewEngine,
        );
        $errorMiddleware->setNext($lastMiddleware);

        return $errorMiddleware;
    }
}
