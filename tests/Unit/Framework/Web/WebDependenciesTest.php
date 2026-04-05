<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web;

use DI\Container;
use Framework\Config\PublicApplicationUrl;
use Framework\Files\DefaultFileManager;
use Framework\HtmlViewEngineSettings;
use Framework\LanguageSettings;
use Framework\Routes\Router;
use Framework\Web\Dependencies;
use Infrastructure\Container\PhpDiMutableContainer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

final class WebDependenciesTest extends TestCase
{
    public function testConfigureRegistersPsrStackAndRequestHandlerWhenRouterPreRegistered(): void
    {
        $basePath = sys_get_temp_dir();
        $di = new Container();
        $di->set(Router::class, new Router());
        $di->set(LanguageSettings::class, new LanguageSettings($basePath));
        $di->set(HtmlViewEngineSettings::class, new HtmlViewEngineSettings($basePath));
        $di->set(DefaultFileManager::class, new DefaultFileManager());
        $di->set(PublicApplicationUrl::class, new PublicApplicationUrl('http://localhost'));

        $mutable = new PhpDiMutableContainer($di);

        Dependencies::configure($mutable);

        $this->assertInstanceOf(Psr17Factory::class, $di->get(Psr17Factory::class));
        $this->assertInstanceOf(ServerRequestCreator::class, $di->get(ServerRequestCreator::class));
        $this->assertInstanceOf(RequestHandlerInterface::class, $di->get(RequestHandlerInterface::class));
    }
}
