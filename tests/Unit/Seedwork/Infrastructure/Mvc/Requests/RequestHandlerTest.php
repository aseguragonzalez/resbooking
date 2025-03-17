<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Requests;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\DataProvider;
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
            Route::create(RouteMethod::Get, Path::create('/test/redirect'), TestController::class, 'redirect'),
            Route::create(RouteMethod::Get, Path::create('/test/get'), TestController::class, 'get'),
            Route::create(RouteMethod::Get, Path::create('/test/search'), TestController::class, 'search'),
            Route::create(RouteMethod::Get, Path::create('/test/find'), TestController::class, 'find'),
            Route::create(RouteMethod::Get, Path::create('/test/{int:id}/list'), TestController::class, 'list'),
            Route::create(RouteMethod::Post, Path::create('/test'), TestController::class, 'edit'),
            Route::create(RouteMethod::Post, Path::create('/test/{int:id}'), TestController::class, 'edit'),
            Route::create(RouteMethod::Post, Path::create('/test/{int:id}/save'), TestController::class, 'save'),
            Route::create(RouteMethod::Post, Path::create('/test/delete'), TestController::class, 'delete'),
            Route::create(RouteMethod::Post, Path::create('/test/custom'), TestController::class, 'custom'),
            Route::create(RouteMethod::Post, Path::create('/test/failed'), TestController::class, 'failed'),
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

    public function testHandleRequestWithRedirectTo(): void
    {
        $uri = $this->requestFactory->createUri('/test/redirect');
        $request = $this->requestFactory->createServerRequest('GET', $uri);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertSame('http://test.com', $response->getHeaderLine('Location'));
        $this->assertEmpty((string) $response->getBody());
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

    public function testHandleGetRequestWithArgs(): void
    {
        $uri = $this->requestFactory->createUri('/test/get');
        $request = $this->requestFactory->createServerRequest('GET', $uri)
            ->withQueryParams(['offset' => 10, 'limit' => 20]);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_get.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    public function testHandleGetRequestWithArgsAndRequestObject(): void
    {
        $uri = $this->requestFactory->createUri('/test/search');
        $request = $this->requestFactory->createServerRequest('GET', $uri)
            ->withQueryParams(['offset' => 1, 'limit' => 20, 'name' => 'John']);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_search.html');
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

    #[DataProvider('postProvider')]
    public function testHandlePostRequest(string $contentType): void
    {
        $uri = $this->requestFactory->createUri('/test/delete');
        $request = $this->requestFactory->createServerRequest('POST', $uri)
            ->withHeader('Content-Type', $contentType);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_delete.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    #[DataProvider('postProvider')]
    public function testHandlePostRequestWithBodyParams(string $contentType): void
    {
        $uri = $this->requestFactory->createUri('/test');
        $request = $this->requestFactory->createServerRequest('POST', $uri)
            ->withHeader('Content-Type', $contentType)
            ->withParsedBody(['name' => 'John Doe', 'email' => 'john.doe@gmail.com', 'id' => 10]);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_edit.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    #[DataProvider('postProvider')]
    public function testHandlePostRequestWithPathAndBodyParams(string $contentType): void
    {
        $uri = $this->requestFactory->createUri('/test/10');
        $request = $this->requestFactory->createServerRequest('POST', $uri)
            ->withHeader('Content-Type', $contentType)
            ->withParsedBody(['name' => 'John Doe', 'email' => 'john.doe@gmail.com']);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_edit.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    #[DataProvider('postProvider')]
    public function testHandlePostRequestWithPathQueryAndBodyParams(string $contentType): void
    {
        $uri = $this->requestFactory->createUri('/test/10/save');
        $request = $this->requestFactory->createServerRequest('POST', $uri)
            ->withHeader('Content-Type', $contentType)
            ->withQueryParams(['offset' => 10, 'limit' => 20])
            ->withParsedBody(['name' => 'John Doe', 'email' => 'john.doe@gmail.com']);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_save.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    #[DataProvider('postProvider')]
    public function testHandlePostRequestWithActionArguments(string $contentType): void
    {
        $uri = $this->requestFactory->createUri('/test/custom');
        $request = $this->requestFactory->createServerRequest('POST', $uri)
            ->withHeader('Content-Type', $contentType)
            ->withParsedBody(['id' => 10, 'amount' => 100.01, 'name' => 'John Doe']);

        $response = $this->requestHandler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $expectedContent = file_get_contents(__DIR__ . '/Files/expected_custom.html');
        $this->assertSame($expectedContent, (string) $response->getBody());
    }

    #[DataProvider('postProvider')]
    public function testHandlePostRequestFailWhenReturnsNoActionResponse(string $contentType): void
    {
        $uri = $this->requestFactory->createUri('/test/failed');
        $request = $this->requestFactory->createServerRequest('POST', $uri)
            ->withHeader('Content-Type', $contentType);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid Response object returned from controller');
        $this->requestHandler->handle($request);
    }

    /**
     * @return array<array{string}>
     */
    public static function postProvider(): array
    {
        return [
            ['application/x-www-form-urlencoded'],
            ['application/json'],
            ['multipart/form-data']
        ];
    }
}
