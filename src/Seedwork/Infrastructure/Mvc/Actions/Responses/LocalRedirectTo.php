<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Actions\Responses;

use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Responses\Headers\{ContentType, Header};
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

final class LocalRedirectTo extends ActionResponse
{
    /**
     * @param class-string $controller
     * @param array<Header> $headers
     */
    private function __construct(
        public readonly string $action,
        public readonly string $controller,
        public readonly ?object $args = null,
        array $headers = [],
    ) {
        parent::__construct(data: new \stdClass(), headers: $headers, statusCode: StatusCode::SeeOther);
    }

    /**
     * @param string $action
     * @param class-string $controller
     * @param object|null $args
     * @param array<Header> $headers
     */
    public static function create(
        string $action,
        string $controller,
        ?object $args = null,
        array $headers = [],
    ): self {
        if (!is_subclass_of($controller, Controller::class)) {
            throw new \InvalidArgumentException("Controller does not exists: $controller");
        }

        if (!method_exists($controller, $action)) {
            throw new \InvalidArgumentException("Action not found: $action");
        }

        $actionMethod = new \ReflectionMethod($controller, $action);
        $requireArguments = $actionMethod->getNumberOfRequiredParameters() > 0;
        if ($requireArguments && is_null($args)) {
            throw new \InvalidArgumentException("Action parameters for $action are required");
        }

        if ($requireArguments && LocalRedirectTo::checkActionArgs($actionMethod, $args) === false) {
            throw new \InvalidArgumentException("Action parameters for $action does not match");
        }

        if (empty(array_filter($headers, fn (Header $header) => $header instanceof ContentType === true))) {
            $headers[] = ContentType::html();
        }
        return new self($action, $controller, $args, $headers);
    }

    private static function checkActionArgs(\ReflectionMethod $actionMethod, object $args): bool
    {
        $argsProperties = get_object_vars($args);
        $requiredActionParameters = array_filter(
            $actionMethod->getParameters(),
            fn (\ReflectionParameter $param) => $param->isOptional() === false
                && $param->allowsNull() === false
                && ($type = $param->getType()) instanceof \ReflectionNamedType
                && $type->getName() !== ServerRequestInterface::class
        );

        foreach ($requiredActionParameters as $parameter) {
            if (!array_key_exists($parameter->getName(), $argsProperties)) {
                return false;
            }
        }

        return true;
    }
}
