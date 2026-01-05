<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use DI\Container;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Seedwork\Infrastructure\Files\DefaultFileManager;
use Seedwork\Infrastructure\Files\FileManager;
use Seedwork\Infrastructure\Mvc\Middlewares\Authentication;
use Seedwork\Infrastructure\Mvc\Middlewares\Authorization;
use Seedwork\Infrastructure\Mvc\Middlewares\ErrorHandling;
use Seedwork\Infrastructure\Mvc\Middlewares\Localization;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Middlewares\RequestHandling;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestHandler;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Views\HtmlViewEngine;
use Seedwork\Infrastructure\Mvc\Views\ViewEngine;

abstract class WebApp
{
    /**
     * @param Container $container
     * @param array<class-string<Middleware>> $middlewares
     */
    protected function __construct(protected readonly Container $container, private array $middlewares = [])
    {
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
        /** @var Authorization $authorizationMiddleware */
        $authorizationMiddleware = $this->container->get(Authorization::class);
        $authorizationMiddleware->setNext($lastMiddleware);
        /** @var Authentication $authenticationMiddleware */
        $authenticationMiddleware = $this->container->get(Authentication::class);
        $authenticationMiddleware->setNext($authorizationMiddleware);
        /** @var Localization $localizationMiddleware */
        $localizationMiddleware = $this->container->get(Localization::class);
        $localizationMiddleware->setNext($authenticationMiddleware);
        /** @var ErrorHandling $errorMiddleware */
        $errorMiddleware = $this->container->get(ErrorHandling::class);
        $errorMiddleware->setNext($localizationMiddleware);
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
}
