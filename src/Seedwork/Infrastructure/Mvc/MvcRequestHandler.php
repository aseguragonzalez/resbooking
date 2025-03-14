<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Seedwork\Infrastructure\Mvc\Routes\{Router, Route, RouteMethod};
use Seedwork\Infrastructure\Mvc\Views\{View, ViewEngine};

final class MvcRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RequestBuilder $requestBuilder,
        private readonly Router $router,
        private readonly ViewEngine $viewEngine,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // 1. from PATH, get controller name and action name
        $path = $request->getUri()->getPath();
        $method = RouteMethod::fromString($request->getMethod());
        $route = $this->router->get($method, $path);
        // 2. create controller instance
        $controller = $this->container->get($route->controller);
        // 3. from request, create action parameters
        $requestObject = $this->getRequestObject($request, $route);
        // 4. call action method with parameters
        $view = $controller->{$route->action}($requestObject);
        if (!$view instanceof View) {
            throw new \Exception('Invalid response object');
        }
        // 5. create response message
        return $this->getResponseMessage($view);
    }

    private function getRequestObject(ServerRequestInterface $request, Route $route): mixed
    {
        $pathArgs = $route->getArgs($request->getUri()->getPath());
        $args = ($request->getMethod() === 'POST')
            ? array_merge($pathArgs, [])
            : array_merge($pathArgs, []);
        return $this->requestBuilder->withRequestType(Route::class)->withArgs($args)->build();
    }

    private function getResponseMessage(View $view): ResponseInterface
    {
        $responseBody = $this->viewEngine->render($view);

        throw new \Exception('Not implemented');
    }
}
