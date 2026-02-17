<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Middlewares;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Framework\Mvc\Middlewares\Middleware;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Security\Identity;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RestaurantContext extends Middleware
{
    public function __construct(
        private readonly RestaurantContextSettings $settings,
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
        if ($path === $this->settings->selectionPath) {
            return $this->next->handleRequest($request);
        }

        $cookies = $request->getCookieParams();
        /** @var string $restaurantId */
        $restaurantId = $cookies[$this->settings->cookieName] ?? '';
        if ($restaurantId !== '' && $this->isRestaurantCookieValid($restaurantId, $identity)) {
            $context->set($this->settings->contextKey, $restaurantId);
            return $this->next->handleRequest($request);
        }

        $backUrl = (string)$request->getUri();
        $selectionUrl = $this->settings->selectionPath . '?backUrl=' . urlencode($backUrl);
        return $this->responseFactory
            ->createResponse(303)
            ->withHeader('Location', $selectionUrl);
    }

    private function isRestaurantCookieValid(string $restaurantId, Identity $identity): bool
    {
        $username = $identity->username();
        $restaurants = $this->restaurantRepository->findByUserEmail($username);
        $matchedRestaurants = array_filter($restaurants, function (Restaurant $restaurant) use ($restaurantId) {
            return $restaurant->getId()->value === $restaurantId;
        });
        return count($matchedRestaurants) > 0;
    }
}
