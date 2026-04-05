<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Web;

use DI\Container;
use Framework\Mvc\MvcWebApp;
use Infrastructure\Container\PhpDiMutableContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

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
}
