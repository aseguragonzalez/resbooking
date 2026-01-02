<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Infrastructure\Ports\Dashboard\DashboardSettings;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Pages\SelectRestaurant;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Requests\SelectRestaurantRequest;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Responses\Headers\SetCookie;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class RestaurantsController extends Controller
{
    public function __construct(
        private readonly RestaurantRepository $restaurantRepository,
        private readonly DashboardSettings $settings,
    ) {
    }

    public function select(ServerRequestInterface $request): ActionResponse
    {
        /** @var RequestContext $context */
        $context = $request->getAttribute(RequestContext::class);
        $identity = $context->getIdentity();
        $userEmail = $identity->username();

        // TODO: use application service instead of repository
        $restaurants = $this->restaurantRepository->findByUserEmail($userEmail);
        $backUrl = $this->getBackUrl($request);

        if (count($restaurants) === 0) {
            $model = SelectRestaurant::withNoRestaurants(backUrl: $backUrl);
            return $this->view(model: $model);
        }

        if (count($restaurants) === 1) {
            $restaurant = $restaurants[0];
            $this->setRestaurantCookie($restaurant->getId());
            return $this->redirectTo($backUrl);
        }

        $restaurantsList = array_map(
            fn ($restaurant) => (object)['id' => $restaurant->getId(), 'name' => $restaurant->getSettings()->name],
            $restaurants
        );
        $model = SelectRestaurant::withRestaurants(restaurants: $restaurantsList, backUrl: $backUrl);
        return $this->view(model: $model);
    }

    public function setRestaurant(
        SelectRestaurantRequest $request,
        ServerRequestInterface $serverRequest
    ): ActionResponse {
        $errors = $request->validate();
        if (!empty($errors)) {
            $backUrl = $this->getBackUrl($serverRequest);
            $model = SelectRestaurant::withErrors(errors: $errors, backUrl: $backUrl);
            return $this->view('select', model: $model);
        }

        $this->setRestaurantCookie($request->restaurantId);
        $backUrl = $request->backUrl ?? $this->getBackUrl($serverRequest);
        return $this->redirectTo($backUrl);
    }

    private function setRestaurantCookie(string $restaurantId): void
    {
        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->settings->restaurantCookieName,
            cookieValue: $restaurantId,
            expires: -1,
        );
        $this->addHeader($setCookieHeader);
    }

    private function getBackUrl(ServerRequestInterface $request): string
    {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['backUrl']) && is_string($queryParams['backUrl'])) {
            return $queryParams['backUrl'];
        }
        return $request->getHeaderLine('Referer') ?: '/';
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/restaurants/select'),
                controller: RestaurantsController::class,
                action: 'select',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/restaurants/select'),
                controller: RestaurantsController::class,
                action: 'setRestaurant',
                authRequired: true
            ),
        ];
    }
}
