<?php

declare(strict_types=1);

namespace Framework\Mvc\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Logging\Logger;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\ErrorMapping;
use Framework\Mvc\ErrorSettings;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Views\ViewEngine;

final class ErrorHandling extends Middleware
{
    public function __construct(
        private readonly ErrorSettings $settings,
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
        } catch (\Throwable $exception) {
            $errorMapping = $this->settings->errorsMapping[get_class($exception)] ?? null;
            return $errorMapping === null
                ? $this->handleException($this->settings->errorsMappingDefaultValue, $request, $exception)
                : $this->handleException($errorMapping, $request, $exception);
        }
    }

    private function handleException(
        ErrorMapping $errorMapping,
        ServerRequestInterface $request,
        \Throwable $exception
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
