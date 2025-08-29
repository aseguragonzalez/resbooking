<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Requests;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Seedwork\Infrastructure\Mvc\Actions\ActionParameterBuilder;
use Seedwork\Infrastructure\Mvc\Actions\Responses\{ActionResponse, LocalRedirectTo, RedirectTo, View};
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Routes\{Router, Route, RouteMethod};
use Seedwork\Infrastructure\Mvc\Views\ViewEngine;

final class RequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ActionParameterBuilder $actionParameterBuilder,
        private readonly ContainerInterface $container,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Router $router,
        private readonly ViewEngine $viewEngine,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->getRouteFromRequest($request);
        $args = $this->getArgsFromRequest($request, $route);
        $actionResponse = $this->executeAction($route, $args);
        return $this->createResponseFromActionResponse($request, $actionResponse);
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
        $this->actionParameterBuilder->withArgs($args);
        return array_map(
            function (\ReflectionParameter $param) use ($args, $request): mixed {
                /** @var \ReflectionNamedType $paramType */
                $paramType = $param->getType();
                /** @var class-string $requestType */
                $requestType = $paramType->getName();
                // If the parameter type is ServerRequestInterface, return the request object
                if ($requestType === ServerRequestInterface::class) {
                    return $request;
                }
                $name = $param->getName();
                $value = $this->getValueOrDefault($name, $args, $param);
                return match ($paramType->getName()) {
                    'int' => filter_var($value, FILTER_VALIDATE_INT),
                    'float' => filter_var($value, FILTER_VALIDATE_FLOAT),
                    'string' => is_string($value) ? (string)$value : null,
                    default => $this->actionParameterBuilder->build($requestType),
                };
            },
            $action->getParameters()
        );
    }

    /**
     * @param array<string, float|int|string> $args
     */
    private function getValueOrDefault(
        string $name,
        array $args,
        \ReflectionParameter $param
    ): mixed {
        return !array_key_exists($name, $args)
            ? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null)
            : $args[$name];
    }

    /**
     * @param array<mixed> $args
     */
    private function executeAction(Route $route, array $args): ActionResponse
    {
        $controller = $this->container->get($route->controller);
        $actionResponse = (count($args) === 0)
            ? $controller->{$route->action}()
            : $controller->{$route->action}(...$args);

        if ($actionResponse instanceof ActionResponse) {
            return $actionResponse;
        }
        throw new \RuntimeException('Invalid Response object returned from controller');
    }

    private function createResponseFromActionResponse(
        ServerRequestInterface $request,
        ActionResponse $actionResponse
    ): ResponseInterface {
        // TODO: create an strategy for handling different ActionResponse types
        if ($actionResponse instanceof LocalRedirectTo) {
            $host = empty($request->getHeaderLine("origin"))
                ? (getenv('DEFAULT_HOST') ?: 'http://localhost:8080')
                : $request->getHeaderLine("origin");
            $newRoute = $this->router->getFromControllerAndAction($actionResponse->controller, $actionResponse->action);
            if (is_null($newRoute)) {
                throw new \RuntimeException(
                    "Route not found for controller: {$actionResponse->controller}, action: {$actionResponse->action}"
                );
            }
            $args = array_map(
                fn ($v) => is_scalar($v) ? (string)$v : ($v === null ? null : ''),
                is_object($actionResponse->args) ? get_object_vars($actionResponse->args) : (array)$actionResponse->args
            );
            $path = $newRoute->getPathFromArgs($args);
            return $this->responseFactory
                ->createResponse(code: StatusCode::SeeOther->value)
                ->withHeader('Location', "{$host}{$path}");
        }

        if ($actionResponse instanceof RedirectTo) {
            $response = $this->responseFactory->createResponse($actionResponse->statusCode->value);
            foreach ($actionResponse->headers as $header) {
                $response = $response->withHeader($header->name, $header->value);
            }
            return $response;
        }

        if ($actionResponse instanceof View) {
            $context = $request->getAttribute(RequestContext::class);
            if (!$context instanceof RequestContext) {
                throw new \RuntimeException('RequestContext not found in request attributes');
            }
            $responseBody = $this->viewEngine->render($actionResponse, $context);
            $response = $this->responseFactory->createResponse($actionResponse->statusCode->value);
            foreach ($actionResponse->headers as $header) {
                $response = $response->withHeader($header->name, $header->value);
            }
            $response->getBody()->write($responseBody);
            return $response;
        }

        throw new \RuntimeException('Invalid ActionResponse type');
    }
}
