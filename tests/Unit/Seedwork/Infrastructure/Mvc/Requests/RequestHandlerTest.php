<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Requests;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Seedwork\Infrastructure\Mvc\Actions\ActionParameterBuilder;
use Seedwork\Infrastructure\Mvc\Requests\RequestHandler;
use Seedwork\Infrastructure\Mvc\Routes\{Path, Route, RouteMethod, Router};
use Seedwork\Infrastructure\Mvc\Views\HtmlViewEngine;
use Seedwork\Infrastructure\Mvc\Views\ViewEngine;
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Requests\TestController;

final class RequestHandlerTest extends TestCase
{
    private ActionParameterBuilder $actionParameterBuilder;
    private ContainerInterface $container;
    private Psr17Factory $requestFactory;
    private ResponseFactoryInterface $responseFactory;
    private Router $router;
    private ViewEngine $viewEngine;
    private RequestHandler $requestHandler;

    protected function setUp(): void
    {
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method('get')->willReturn(new TestController());
        $this->actionParameterBuilder = new ActionParameterBuilder();
        $this->container = $containerMock;
        $this->requestFactory = new Psr17Factory();
        $this->responseFactory = new Psr17Factory();
        $this->router = new Router(routes: [
            Route::create(RouteMethod::Get, Path::create('/test'), TestController::class, 'index'),
            Route::create(RouteMethod::Get, Path::create('/test/find'), TestController::class, 'find'),
            Route::create(RouteMethod::Get, Path::create('/test/{int:id}/list'), TestController::class, 'list')
        ]);
        $this->viewEngine = new HtmlViewEngine(basePath: __DIR__ . '/Views');
        $this->requestHandler = new RequestHandler(
            $this->actionParameterBuilder,
            $this->container,
            $this->responseFactory,
            $this->router,
            $this->viewEngine
        );
    }

    protected function tearDown(): void
    {
    }

    public function testHandleGetRequest(): void
    {
        $uri = $this->requestFactory->createUri('/test');
        $request = $this->requestFactory->createServerRequest('GET', $uri);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_index.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    public function testHandleGetRequestWithQueryParams(): void
    {
        $uri = $this->requestFactory->createUri('/test/find');
        $request = $this->requestFactory->createServerRequest('GET', $uri)
            ->withQueryParams(['offset' => 10, 'limit' => 20]);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_find.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    public function testHandleGetRequestWithPathAndQueryParams(): void
    {
        $uri = $this->requestFactory->createUri('/test/10/list');
        $request = $this->requestFactory->createServerRequest('GET', $uri)
            ->withQueryParams(['offset' => 10, 'limit' => 20]);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_list.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }
}
