<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Middlewares;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Infrastructure\Ports\Dashboard\DashboardSettings;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Middlewares\Middleware;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Security\Identity;

final class RestaurantContext extends Middleware
{
    public function __construct(
        private readonly DashboardSettings $settings,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RestaurantRepository $restaurantRepository,
        ?Middleware $next = null
    ) {
        parent::__construct($next);
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->next === null) {
            throw new \RuntimeException('No middleware to handle the request');
        }

        /** @var RequestContext $context */
        $context = $request->getAttribute(RequestContext::class);
        $identity = $context->getIdentity();
        if (!$identity->isAuthenticated()) {
            return $this->next->handleRequest($request);
        }

        $path = $request->getUri()->getPath();
        if ($path === $this->settings->restaurantSelectionUrl) {
            return $this->next->handleRequest($request);
        }

        $cookies = $request->getCookieParams();
        /** @var string $restaurantId */
        $restaurantId = $cookies[$this->settings->restaurantCookieName] ?? '';
        if ($restaurantId !== '' && $this->isRestaurantCookieValid($restaurantId, $identity)) {
            $context->set($this->settings->restaurantIdContextKey, $restaurantId);
            return $this->next->handleRequest($request);
        }

        $backUrl = $request->getUri()->__toString();
        $selectionUrl = $this->settings->restaurantSelectionUrl . '?backUrl=' . urlencode($backUrl);
        return $this->responseFactory
            ->createResponse(303)
            ->withHeader('Location', $selectionUrl);
    }

    private function isRestaurantCookieValid(string $restaurantId, Identity $identity): bool
    {
        $restaurants = $this->restaurantRepository->findByUserEmail($identity->username());
        $matchedRestaurants = array_filter($restaurants, function (Restaurant $restaurant) use ($restaurantId) {
            return $restaurant->getId() === $restaurantId;
        });
        return count($matchedRestaurants) > 0;
    }
}
