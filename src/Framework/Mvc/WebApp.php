<?php

declare(strict_types=1);

namespace Framework\Mvc;

use DI\Container;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
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

abstract class WebApp
{
    /**
     * @param Container $container
     * @param array<class-string<Middleware>> $middlewares
     */
    protected function __construct(
        protected readonly Container $container,
        private array $middlewares = [],
        private bool $requireAuthentication = false,
        private bool $requireAuthorization = false,
    ) {
    }

    abstract protected function configure(): void;

    abstract protected function configureSettings(): void;

    abstract protected function router(): Router;

    /**
     * @param class-string<Middleware> $middleware
     */
    protected function addMiddleware(string $middleware): void
    {
        $this->middlewares[] = $middleware;
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

    public function handleRequest(): void
    {
        $requestContext = new RequestContext();
        $this->container->set(RequestContext::class, $requestContext);
        $this->configureSettings();
        $this->configure();
        $this->configureMvc();
        $this->buildMiddlewareChain();

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

    protected function useAuthentication(): void
    {
        $this->requireAuthentication = true;
    }

    protected function useAuthorization(): void
    {
        $this->requireAuthorization = true;
    }
}
