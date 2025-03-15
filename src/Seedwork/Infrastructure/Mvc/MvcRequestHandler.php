<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Seedwork\Infrastructure\Mvc\Requests\RequestBuilder;
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
        $route = $this->getRouteFromRequest($request);
        $args = $this->getArgsFromRequest($request, $route);
        $view = $this->executeAction($route, $args);
        return $this->createResponseFromView($view);
    }

    private function getRouteFromRequest(ServerRequestInterface $request): Route
    {
        $path = $request->getUri()->getPath();
        $method = RouteMethod::fromString($request->getMethod());
        return $this->router->get($method, $path);
    }

    /**
     * @return array<mixed>
     */
    private function getArgsFromRequest(ServerRequestInterface $request, Route $route): array
    {
        /** @var array<string, float|int|string> $args */
        $args = array_merge($request->getQueryParams(), $route->getArgs($request->getUri()->getPath()));
        if ($route->method === RouteMethod::Post) {
            $parsedBody = $request->getParsedBody();
            if (!is_null($parsedBody) && is_array($parsedBody)) {
                /** @var array<string, float|int|string> $args */
                $args = array_merge($args, (array)$parsedBody);
            }
        }
        $action = new \ReflectionMethod($route->controller, $route->action);
        $this->requestBuilder->withArgs($args);
        return array_map(
            function (\ReflectionParameter $param): mixed {
                $paramType = $param->getType();
                if (!$paramType instanceof \ReflectionNamedType) {
                    throw new \Exception('Not implemented');
                }
                /** @var class-string $requestType */
                $requestType = $paramType->getName();
                return $this->requestBuilder->build($requestType);
            },
            $action->getParameters()
        );
    }

    /**
     * @param array<mixed> $args
     */
    private function executeAction(Route $route, array $args): mixed
    {
        $controller = $this->container->get($route->controller);
        if (count($args) === 0) {
            return $controller->{$route->action}();
        }
        return $controller->{$route->action}(...$args);
    }

    private function createResponseFromView(mixed $view): ResponseInterface
    {
        if (!$view instanceof View) {
            throw new \Exception('Not implemented');
        }
        $responseBody = $this->viewEngine->render($view);
        throw new \Exception('Not implemented');
    }
}
