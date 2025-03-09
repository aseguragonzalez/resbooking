<?php

declare(strict_types=1);

namespace App\Seedwork\Infrastructure\Mvc;

use App\Seedwork\Infrastructure\Mvc\Routes\{Router, RouteMethod};
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Seedwork\Infrastructure\Mvc\Responses\Response;

final class MvcRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Router $router,
        private readonly ContainerInterface $container
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
        if (!method_exists($route->controller, $route->action)) {
            throw new \Exception('Invalid action method');
        }
        if (!is_callable([$controller, $route->action])) {
            throw new \Exception('Invalid action method');
        }

        // 3. from request, create action parameters
        $requestObject = $this->getRequestObject($request, requestTypeName: $route->request);
        if (gettype($requestObject) !== $route->request || $requestObject instanceof $route->request) {
            throw new \Exception('Invalid request object');
        }

        // 4. call action method with parameters
        $response = $controller->{$route->action}($requestObject);
        // call_user_func_array([$controller, $route->action], [$requestObject]);
        if (!$response instanceof Response) {
            throw new \Exception('Invalid response object');
        }

        // 5. create httpResponse object
        // 6. return response object
        throw new \Exception('Not implemented');
    }

    private function getRequestObject(ServerRequestInterface $request, string $requestTypeName): object
    {
        if ($request == null) {
            return new \Exception('Not implemented');
        }
        if ($requestTypeName === '') {
            return new \Exception('Not implemented');
        }
        throw new \Exception('Not implemented');
    }
}
