<?php

declare(strict_types=1);

namespace Framework\Mvc;

use DI\Container;
use Framework\Application;
use Framework\Files\DefaultFileManager;
use Framework\Files\FileManager;
use Framework\Mvc\Middlewares\Authentication;
use Framework\Mvc\Middlewares\Authorization;
use Framework\Mvc\Middlewares\ErrorHandling;
use Framework\Mvc\Middlewares\Localization;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Middlewares\RequestHandling;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Requests\RequestHandler;
use Framework\Mvc\Routes\Router;
use Framework\Mvc\Views\HtmlViewEngine;
use Framework\Mvc\Views\ViewEngine;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The base class for all MVC Web applications.
 */
abstract class MvcWebApp extends Application
{
    /**
     * @param Container $container The container instance.
     * @param string $basePath The base path of the application.
     * @param array<class-string<Middleware>> $middlewares The middlewares to use.
     * @param bool $requireAuthentication Whether to require authentication.
     * @param bool $requireAuthorization Whether to require authorization.
     */
    protected function __construct(
        Container $container,
        string $basePath,
        private array $middlewares = [],
        private bool $requireAuthentication = false,
        private bool $requireAuthorization = false,
    ) {
        parent::__construct($container, $basePath);
    }

    /**
     * Configure the router for the application.
     */
    abstract protected function router(): Router;

    /**
     * @param int|null $argc The number of arguments passed to the application. Default is null.
     * @param array<string> $argv The arguments to pass to the application. Default is an empty array.
     * @return int The exit code of the application.
     */
    public function run(?int $argc = null, array $argv = []): int
    {
        $requestContext = new RequestContext();
        $this->container->set(RequestContext::class, $requestContext);
        $this->configureSettings();
        $this->configureLogging();
        $this->configureDependencies();
        $this->configureMvc();
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
     * Require authorization for the application.
     */
    public function useAuthorization(): void
    {
        $this->requireAuthorization = true;
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

        // Configure fixed middlewares: Authorization, Authentication, Localization, ErrorHandling
        if ($this->requireAuthorization && $this->requireAuthentication) {
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

        /** @var Localization $localizationMiddleware */
        $localizationMiddleware = $this->container->get(Localization::class);
        $localizationMiddleware->setNext($lastMiddleware);
        $lastMiddleware = $localizationMiddleware;

        /** @var ErrorHandling $errorMiddleware */
        $errorMiddleware = $this->container->get(ErrorHandling::class);
        $errorMiddleware->setNext($lastMiddleware);
        $this->container->set(Middleware::class, $errorMiddleware);
    }

    private function configureMvc(): void
    {
        $psr17Factory = new Psr17Factory();
        $this->container->set(Psr17Factory::class, $psr17Factory);
        $this->container->set(ResponseFactoryInterface::class, $psr17Factory);
        $this->container->set(ServerRequestCreator::class, new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
        ));
        $this->container->set(Router::class, $this->router());
        $this->container->set(FileManager::class, $this->container->get(DefaultFileManager::class));
        $this->container->set(ViewEngine::class, $this->container->get(HtmlViewEngine::class));
        $this->container->set(RequestHandlerInterface::class, $this->container->get(RequestHandler::class));
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
