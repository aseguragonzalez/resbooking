<?php

declare(strict_types=1);

namespace Framework\Mvc;

use Framework\Mvc\Container\MutableContainer;
use Framework\Mvc\Middlewares\AllowedHttpMethodsForHtmlUi;
use Framework\Mvc\Middlewares\Authentication;
use Framework\Mvc\Middlewares\Authorization;
use Framework\Mvc\Middlewares\CsrfProtection;
use Framework\Mvc\Middlewares\ErrorHandling;
use Framework\Mvc\Middlewares\Localization;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Middlewares\RequestHandling;
use Framework\Mvc\Requests\RequestContext;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;

/**
 * The base class for all MVC Web applications.
 */
abstract class MvcWebApp extends Application
{
    /**
     * @param ContainerInterface $container PSR-11 container. For {@see run()}, the instance must also implement
     *        {@see MutableContainer} so request-scoped services can be registered.
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

    /**
     * @param int|null $argc The number of arguments passed to the application. Default is null.
     * @param array<string> $argv The arguments to pass to the application. Default is an empty array.
     * @return int The exit code of the application.
     */
    public function run(?int $argc = null, array $argv = []): int
    {
        $requestContext = new RequestContext();
        $this->mutableContainer()->set(RequestContext::class, $requestContext);
        $this->buildMiddlewareChain();
        $this->handleRequest($requestContext);

        return 0;
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

    private function buildMiddlewareChain(): void
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
        $this->mutableContainer()->set(Middleware::class, $errorMiddleware);
    }

    /**
     * @throws \LogicException When the container does not support runtime registration.
     */
    private function mutableContainer(): MutableContainer
    {
        if (!$this->container instanceof MutableContainer) {
            throw new \LogicException(
                'MvcWebApp::run() requires a container implementing ' . MutableContainer::class
                . ' (e.g. Infrastructure\\Container\\PhpDiMutableContainer).'
            );
        }

        return $this->container;
    }

    private function handleRequest(RequestContext $requestContext): void
    {
        $requestCreator = $this->container->get(ServerRequestCreator::class);
        if (!$requestCreator instanceof ServerRequestCreator) {
            throw new \RuntimeException('ServerRequestCreator not found in container');
        }

        $middlewareChain = $this->container->get(Middleware::class);
        if (!$middlewareChain instanceof Middleware) {
            throw new \RuntimeException('Middleware not found in container');
        }

        $request = $requestCreator->fromGlobals();
        $response = $middlewareChain->handleRequest(
            $request->withAttribute(RequestContext::class, $requestContext)
        );

        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        echo $response->getBody();
    }
}
