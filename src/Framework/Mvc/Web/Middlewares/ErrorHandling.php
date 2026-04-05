<?php

declare(strict_types=1);

namespace Framework\Mvc\Middlewares;

use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\ErrorMapping;
use Framework\Mvc\ErrorSettings;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Views\ViewEngine;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Outermost middleware: catches throwables, logs them, and renders an HTML error view.
 *
 * {@see ErrorSettings} maps exception types to status code and template. Resolution walks the
 * exception class hierarchy (concrete class first, then each parent) and uses the first mapping
 * found; if none match, {@see ErrorSettings::$errorsMappingDefaultValue} is used.
 */
final class ErrorHandling extends Middleware
{
    public function __construct(
        private readonly ErrorSettings $settings,
        private readonly LoggerInterface $logger,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ViewEngine $viewEngine,
        ?Middleware $next = null
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        try {
            return $this->next->handleRequest($request);
        } catch (\Throwable $exception) {
            $errorMapping = $this->resolveErrorMapping($exception);

            return $this->handleException($errorMapping, $request, $exception);
        }
    }

    private function resolveErrorMapping(\Throwable $exception): ErrorMapping
    {
        for (
            $class = $exception::class;
            $class !== false;
            $class = get_parent_class($class)
        ) {
            $mapping = $this->settings->errorsMapping[$class] ?? null;
            if ($mapping !== null) {
                return $mapping;
            }
        }

        return $this->settings->errorsMappingDefaultValue;
    }

    private function handleException(
        ErrorMapping $errorMapping,
        ServerRequestInterface $request,
        \Throwable $exception
    ): ResponseInterface {
        $this->logger->error('Error handling middleware: {message}', ['message' => $exception->getMessage()]);
        /** @var RequestContext $context */
        $context = $request->getAttribute(RequestContext::class);
        $responseBody = $this->viewEngine->render(new View($errorMapping->templateName, $errorMapping), $context);
        $response = $this->responseFactory->createResponse($errorMapping->statusCode);
        $response->getBody()->write($responseBody);
        return $response;
    }
}
