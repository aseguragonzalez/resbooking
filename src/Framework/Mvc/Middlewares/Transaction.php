<?php

declare(strict_types=1);

namespace Framework\Mvc\Middlewares;

use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Transaction extends Middleware
{
    public function __construct(
        private readonly PDO $pdo,
        ?Middleware $next = null
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        if ($request->getMethod() !== 'POST') {
            return $this->next->handleRequest($request);
        }

        $this->pdo->beginTransaction();

        try {
            $response = $this->next->handleRequest($request);
            $this->pdo->commit();
            return $response;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
