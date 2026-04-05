<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Web;

use DI\Container;
use Framework\Mvc\MvcWebApp;
use Framework\Mvc\Requests\RequestContext;
use Infrastructure\Container\PhpDiMutableContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class MvcWebAppTest extends TestCase
{
    public function testUseRouteAccessControlWithoutAuthenticationThrows(): void
    {
        $app = new class (new PhpDiMutableContainer(new Container()), '/tmp', new RequestContext()) extends MvcWebApp {
            public function __construct(ContainerInterface $container, string $basePath, RequestContext $requestContext)
            {
                parent::__construct($container, $basePath, $requestContext);
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Route access control requires authentication');
        $app->useRouteAccessControl();
    }

    public function testUseRouteAccessControlAfterAuthenticationDoesNotThrow(): void
    {
        $app = new class (new PhpDiMutableContainer(new Container()), '/tmp', new RequestContext()) extends MvcWebApp {
            public function __construct(ContainerInterface $container, string $basePath, RequestContext $requestContext)
            {
                parent::__construct($container, $basePath, $requestContext);
            }
        };

        $app->useAuthentication();
        $app->useRouteAccessControl();
        $this->addToAssertionCount(1);
    }
}
