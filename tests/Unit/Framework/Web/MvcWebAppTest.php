<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web;

use DI\Container;
use DI\NotFoundException;
use Framework\MvcWebApp;
use Infrastructure\Container\PhpDiMutableContainer;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MvcWebAppTest extends TestCase
{
    public function testUseRouteAccessControlWithoutAuthenticationThrows(): void
    {
        $app = new class (new PhpDiMutableContainer(new Container()), '/tmp') extends MvcWebApp {
            public function __construct(ContainerInterface $container, string $basePath)
            {
                parent::__construct($container, $basePath);
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Route access control requires authentication');
        $app->useRouteAccessControl();
    }

    public function testUseRouteAccessControlAfterAuthenticationDoesNotThrow(): void
    {
        $app = new class (new PhpDiMutableContainer(new Container()), '/tmp') extends MvcWebApp {
            public function __construct(ContainerInterface $container, string $basePath)
            {
                parent::__construct($container, $basePath);
            }
        };

        $app->useAuthentication();
        $app->useRouteAccessControl();
        $this->addToAssertionCount(1);
    }

    public function testRunReturnsZeroOnSuccess(): void
    {
        $app = new class (new PhpDiMutableContainer(new Container()), '/tmp') extends MvcWebApp {
            public function __construct(ContainerInterface $container, string $basePath)
            {
                parent::__construct($container, $basePath);
            }

            public function handleRequest(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, [], '');
            }
        };

        ob_start();
        $exitCode = $app->run($this->createStub(ServerRequestInterface::class));
        ob_end_clean();

        $this->assertSame(0, $exitCode);
    }

    public function testRunReturnsExitCodeForNotFoundException(): void
    {
        $app = $this->createThrowingApp(new NotFoundException('missing'));
        $this->assertSame(2, $app->run($this->createStub(ServerRequestInterface::class)));
    }

    public function testRunReturnsExitCodeForInvalidArgumentException(): void
    {
        $app = $this->createThrowingApp(new \InvalidArgumentException('bad arg'));
        $this->assertSame(3, $app->run($this->createStub(ServerRequestInterface::class)));
    }

    public function testRunReturnsExitCodeForLogicException(): void
    {
        $app = $this->createThrowingApp(new \LogicException('logic'));
        $this->assertSame(4, $app->run($this->createStub(ServerRequestInterface::class)));
    }

    public function testRunReturnsExitCodeForRuntimeException(): void
    {
        $app = $this->createThrowingApp(new \RuntimeException('runtime'));
        $this->assertSame(5, $app->run($this->createStub(ServerRequestInterface::class)));
    }

    public function testRunReturnsExitCodeOneForGenericException(): void
    {
        $app = $this->createThrowingApp(new \Exception('generic'));
        $this->assertSame(1, $app->run($this->createStub(ServerRequestInterface::class)));
    }

    private function createThrowingApp(\Throwable $toThrow): MvcWebApp
    {
        return new class (new PhpDiMutableContainer(new Container()), '/tmp', $toThrow) extends MvcWebApp {
            public function __construct(
                ContainerInterface $container,
                string $basePath,
                private readonly \Throwable $toThrow,
            ) {
                parent::__construct($container, $basePath);
            }

            public function handleRequest(ServerRequestInterface $request): ResponseInterface
            {
                throw $this->toThrow;
            }
        };
    }
}
