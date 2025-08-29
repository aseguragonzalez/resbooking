<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Views\ViewEngine;
use Seedwork\Infrastructure\Mvc\ErrorMapping;
use Seedwork\Infrastructure\Mvc\Settings;

final class ErrorHandling extends Middleware
{
    public function __construct(
        private readonly Settings $settings,
        private readonly Logger $logger,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ViewEngine $viewEngine,
        ?Middleware $next = null
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            $exception = new \RuntimeException('No middleware to handle the request');
            $this->logger->error('Error handling middleware: no next middleware available', $exception);
            throw $exception;
        }

        try {
            return $this->next->handleRequest($request);
        } catch (\Exception $exception) {
            $errorMapping = $this->settings->errorsMapping[get_class($exception)] ?? null;
            return $errorMapping === null
                ? $this->handleException($this->settings->errorsMappingDefaultValue, $request, $exception)
                : $this->handleException($errorMapping, $request, $exception);
        }
    }

    private function handleException(
        ErrorMapping $errorMapping,
        ServerRequestInterface $request,
        \Exception $exception
    ): ResponseInterface {
        $this->logger->error('Error handling middleware: ' . $errorMapping->templateName, $exception);
        /** @var RequestContext $context */
        $context = $request->getAttribute(RequestContext::class);
        $responseBody = $this->viewEngine->render(new View($errorMapping->templateName, $errorMapping), $context);
        $response = $this->responseFactory->createResponse($errorMapping->statusCode);
        $response->getBody()->write($responseBody);
        return $response;
    }
}
