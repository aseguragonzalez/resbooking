<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use DI\Container;
use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Controllers\ReservationsController;
use Monolog\Logger as MonoLogger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\Actions\ActionParameterBuilder;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestContextKeys;
use Seedwork\Infrastructure\Mvc\Requests\RequestHandler;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Views\{BranchesReplacer, I18nReplacer, ModelReplacer, HtmlViewEngine, ViewEngine};
use Seedwork\Infrastructure\Logging\MonologLogger;

class WebApp
{
    public function __construct(private readonly Container $container, private readonly Settings $settings)
    {
    }

    protected function configure(Container $container): void
    {
        // read context data from the environment
        $context = [
            "service.name" => "my-app",
            "service.version" => "1.0.0",
            "environment" => "local",
        ];

        // configure logger
        $logger = new MonoLogger($context["service.name"]);
        $handler = new StreamHandler('php://stdout', Level::Debug);
        $handler->setFormatter(new JsonFormatter());
        $logger->pushHandler($handler);
        $container->set(Logger::class, new MonologLogger($logger, $context));
    }

    protected function router(): Router
    {
        return new Router(routes:[
            Route::create(RouteMethod::Get, Path::create('/'), DashboardController::class, 'index'),
            Route::create(RouteMethod::Get, Path::create('/reservations'), ReservationsController::class, 'index'),
            Route::create(
                RouteMethod::Get,
                Path::create('/reservations/create'),
                ReservationsController::class,
                'create'
            ),
            Route::create(RouteMethod::Get, Path::create('/reservations/{id}'), ReservationsController::class, 'edit'),
            Route::create(
                RouteMethod::Post,
                Path::create('/reservations/{id}'),
                ReservationsController::class,
                'update'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/reservations/{id}/status'),
                ReservationsController::class,
                'updateStatus'
            )
        ]);
    }

    private function dependenciesConfiguration(): void
    {
        $psr17Factory = new Psr17Factory();
        $this->container->set(Psr17Factory::class, $psr17Factory);
        $this->container->set(ResponseFactoryInterface::class, $psr17Factory);
        $this->container->set(ServerRequestCreator::class, new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
        ));
        $this->container->set(ActionParameterBuilder::class, new ActionParameterBuilder());
        $this->container->set(Settings::class, $this->settings);
        $this->container->set(Router::class, $this->router());

        $i18nReplacer = new I18nReplacer($this->settings, new BranchesReplacer(new ModelReplacer()));
        $this->container->set(ViewEngine::class, new HtmlViewEngine($this->settings, $i18nReplacer));
    }

    public function onRequest(): void
    {
        $this->dependenciesConfiguration();
        $this->configure($this->container);

        $requestCreator = $this->container->get(ServerRequestCreator::class);
        if (!$requestCreator instanceof ServerRequestCreator) {
            throw new \RuntimeException('ServerRequestCreator not found in container');
        }
        $request = $requestCreator->fromGlobals();
        $requestContext = new RequestContext();
        $requestContext->set(RequestContextKeys::LANGUAGE->value, 'en-en');

        $requestHandler = $this->container->get(RequestHandler::class);
        if (!$requestHandler instanceof RequestHandler) {
            throw new \RuntimeException('RequestHandler not found in container');
        }
        $response = $requestHandler->handle($request->withAttribute(RequestContext::class, $requestContext));
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        echo $response->getBody();
    }
}
