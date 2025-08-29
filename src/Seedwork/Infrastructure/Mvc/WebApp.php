<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use DI\Container;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\Actions\ActionParameterBuilder;
use Seedwork\Infrastructure\Mvc\Middlewares\{
    Authentication,
    Authorization,
    ErrorHandling,
    Localization,
    Middleware,
    RequestHandling
};
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Routes\Router;
use Seedwork\Infrastructure\Mvc\Views\{BranchesReplacer, I18nReplacer, ModelReplacer, HtmlViewEngine, ViewEngine};

abstract class WebApp
{
    /**
     * @param Settings $settings
     * @param Container $container
     */
    protected function __construct(
        protected readonly Settings $settings,
        protected readonly Container $container = new Container(),
    ) {
    }

    abstract protected function configure(): void;

    abstract protected function router(): Router;

    private function configureMvc(): void
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

        /** @var RequestHandling $requestHandlingMiddleware */
        $requestHandlingMiddleware = $this->container->get(RequestHandling::class);
        /** @var Authorization $authorizationMiddleware */
        $authorizationMiddleware = $this->container->get(Authorization::class);
        $authorizationMiddleware->setNext($requestHandlingMiddleware);
        /** @var Authentication $authenticationMiddleware */
        $authenticationMiddleware = $this->container->get(Authentication::class);
        $authenticationMiddleware->setNext($authorizationMiddleware);
        /** @var Localization $localizationMiddleware */
        $localizationMiddleware = $this->container->get(Localization::class);
        $localizationMiddleware->setNext($authenticationMiddleware);
        /** @var ErrorHandling $errorMiddleware */
        $errorMiddleware = $this->container->get(ErrorHandling::class);
        $errorMiddleware->setNext($localizationMiddleware);
        $this->container->set(Middleware::class, $errorMiddleware);
    }

    public function onRequest(): void
    {
        $this->configure();
        $this->configureMvc();

        $requestCreator = $this->container->get(ServerRequestCreator::class);
        if (!$requestCreator instanceof ServerRequestCreator) {
            throw new \RuntimeException('ServerRequestCreator not found in container');
        }

        $middlewareChain = $this->container->get(Middleware::class);
        if (!$middlewareChain instanceof Middleware) {
            throw new \RuntimeException('Middleware not found in container');
        }

        $request = $requestCreator->fromGlobals();
        $response = $middlewareChain->handleRequest(
            $request->withAttribute(RequestContext::class, new RequestContext())
        );

        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        echo $response->getBody();
    }
}
