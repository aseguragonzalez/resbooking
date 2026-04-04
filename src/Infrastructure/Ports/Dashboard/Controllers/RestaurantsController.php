<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Framework\Mvc\Actions\MvcAction;
use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\Controllers\Controller;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\Headers\SetCookie;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Pages\SelectRestaurant;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Requests\SelectRestaurantRequest;
use Psr\Http\Message\ServerRequestInterface;

final class RestaurantsController extends Controller
{
    public function __construct(
        private readonly RestaurantRepository $restaurantRepository,
        private readonly RestaurantContextSettings $settings,
    ) {
        parent::__construct();
    }

    #[MvcAction]
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
            return $this->view('Restaurants/select', model: $model);
        }

        if (count($restaurants) === 1) {
            $restaurant = $restaurants[0];
            $this->setRestaurantCookie($restaurant->id->value);
            return $this->redirectTo($backUrl);
        }

        $restaurantsList = array_map(
            fn ($restaurant) => (object)[
                'id' => $restaurant->id->value,
                'name' => $restaurant->settings->name,
            ],
            $restaurants
        );
        $model = SelectRestaurant::withRestaurants(restaurants: $restaurantsList, backUrl: $backUrl);
        return $this->view('Restaurants/select', model: $model);
    }

    #[MvcAction]
    public function setRestaurant(
        SelectRestaurantRequest $request,
        ServerRequestInterface $serverRequest
    ): ActionResponse {
        $errors = $request->validate();
        if (!empty($errors)) {
            $backUrl = $this->getBackUrl($serverRequest);
            $model = SelectRestaurant::withErrors(errors: $errors, backUrl: $backUrl);
            return $this->view('Restaurants/select', model: $model);
        }

        $this->setRestaurantCookie($request->restaurantId);
        $backUrl = $request->backUrl ?? $this->getBackUrl($serverRequest);
        return $this->redirectTo($backUrl);
    }

    private function setRestaurantCookie(string $restaurantId): void
    {
        $setCookieHeader = SetCookie::createSecureCookie(
            cookieName: $this->settings->cookieName,
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
