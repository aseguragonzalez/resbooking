<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web;

use DI\Container;
use Framework\Web\WebApplication;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class WebApplicationTest extends TestCase
{
    public function testBasePathReturnsConstructorValue(): void
    {
        $app = new class (new Container(), '/app/root') extends WebApplication {
            public function __construct(Container $container, string $basePath)
            {
                parent::__construct($container, $basePath);
            }

            public function run(ServerRequestInterface $request): int
            {
                return 0;
            }
        };

        $this->assertSame('/app/root', $app->basePath());
    }
}
