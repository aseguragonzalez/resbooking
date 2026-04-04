<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Web;

use DI\Container;
use Framework\Mvc\Container\PhpDiServiceRegistry;
use Framework\Mvc\Container\ServiceRegistry;
use Framework\Mvc\MvcWebApp;
use Framework\Mvc\Routes\Router;
use PHPUnit\Framework\TestCase;

final class MvcWebAppTest extends TestCase
{
    public function testUseRouteAccessControlWithoutAuthenticationThrows(): void
    {
        $app = new class (new PhpDiServiceRegistry(new Container()), '/tmp') extends MvcWebApp {
            public function __construct(ServiceRegistry $container, string $basePath)
            {
                parent::__construct($container, $basePath);
            }

            protected function router(): Router
            {
                return new Router();
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Route access control requires authentication');
        $app->useRouteAccessControl();
    }

    public function testUseRouteAccessControlAfterAuthenticationDoesNotThrow(): void
    {
        $app = new class (new PhpDiServiceRegistry(new Container()), '/tmp') extends MvcWebApp {
            public function __construct(ServiceRegistry $container, string $basePath)
            {
                parent::__construct($container, $basePath);
            }

            protected function router(): Router
            {
                return new Router();
            }
        };

        $app->useAuthentication();
        $app->useRouteAccessControl();
        $this->addToAssertionCount(1);
    }
}
