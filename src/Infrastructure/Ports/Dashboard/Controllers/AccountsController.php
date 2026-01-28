<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurant;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurantCommand;
use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\AuthSettings;
use Framework\Mvc\Controllers\Controller;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Responses\Headers\SetCookie;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use Framework\Mvc\Security\Domain\Exceptions\InvalidCredentialsException;
use Framework\Mvc\Security\Domain\Exceptions\ResetPasswordChallengeException;
use Framework\Mvc\Security\Domain\Exceptions\SignUpChallengeException;
use Framework\Mvc\Security\Domain\Exceptions\UserIsNotActiveException;
use Framework\Mvc\Security\IdentityManager;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPassword;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPasswordChallenge;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\SignIn;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\SignUp;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ConfirmResetPasswordRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ResetPasswordRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignInRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignUpRequest;
use Psr\Http\Message\ServerRequestInterface;

final class AccountsController extends Controller
{
    public function __construct(
        private readonly CreateNewRestaurant $createNewRestaurant,
        private readonly IdentityManager $identityManager,
        private readonly AuthSettings $settings,
        private readonly RequestContext $requestContext,
    ) {
        parent::__construct();
    }

    public function signIn(): ActionResponse
    {
        if ($this->isUserAuthenticated()) {
            return $this->redirectToAction("index", DashboardController::class);
        }
        return $this->view(model: SignIn::new());
    }

    public function signInUser(SignInRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->view("signIn", model: SignIn::withErrors($errors));
        }

        try {
            $challenge = $this->identityManager->signIn(
                $request->username,
                $request->password,
                $request->keepMeSignedIn()
            );
            $setCookieHeader = SetCookie::createSecureCookie(
                cookieName: $this->settings->cookieName,
                cookieValue: $challenge->getToken(),
                expires: $challenge->getExpiresAt()->getTimestamp(),
            );
            $this->addHeader($setCookieHeader);
            return $this->redirectToAction("index", DashboardController::class);
        } catch (InvalidCredentialsException) {
            return $this->view("signIn", model: SignIn::withErrors([
                'username' => '{{accounts.signin.form.invalid-credentials}}',
            ]));
        } catch (UserIsNotActiveException) {
            return $this->view("signIn", model: SignIn::withErrors([
                'username-inactive' => '{{accounts.signin.form.inactive-user}}',
            ]));
        }
    }

    public function signOut(ServerRequestInterface $request): ActionResponse
    {
        $token = $request->getCookieParams()['auth'] ?? '';
        if (!is_string($token) || empty($token)) {
            return $this->redirectToAction("signIn");
        }

        $this->identityManager->signOut($token);

        $currentCookies = $request->getCookieParams();
        $cookieNames = array_keys($currentCookies);
        foreach ($cookieNames as $cookieName) {
            $this->addHeader(SetCookie::removeCookie($cookieName));
        }

        return $this->redirectToAction("signIn");
    }

    public function signUp(): ActionResponse
    {
        if ($this->isUserAuthenticated()) {
            return $this->redirectToAction("index", DashboardController::class);
        }
        return $this->view(model: SignUp::new());
    }

    public function signUpUser(SignUpRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->view("signUp", model: SignUp::withErrors($errors));
        }

        $this->identityManager->signUp(
            $request->username,
            $request->password,
            ['admin']
        );

        $this->createNewRestaurant->execute(new CreateNewRestaurantCommand(email: $request->username));

        return $this->redirectToAction("signIn");
    }

    public function activateUser(string $token): ActionResponse
    {
        try {
            $this->identityManager->activateUserIdentity($token);
        } catch (SignUpChallengeException) {
            return $this->view("tokenExpired");
        }
        return $this->redirectToAction("signIn");
    }

    public function resetPassword(): ActionResponse
    {
        if ($this->isUserAuthenticated()) {
            return $this->redirectToAction("index", DashboardController::class);
        }
        return $this->view(model: ResetPassword::new());
    }

    public function sendResetPasswordEmail(ResetPasswordRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->view("resetPassword", model: ResetPassword::withErrors($errors));
        }

        $this->identityManager->resetPasswordChallenge($request->username);
        return $this->view("resetPassword", model: ResetPassword::succeeded());
    }

    public function resetPasswordChallenge(string $token): ActionResponse
    {
        if ($this->isUserAuthenticated()) {
            return $this->redirectToAction("index", DashboardController::class);
        }
        return $this->view(model: ResetPasswordChallenge::new($token));
    }

    public function confirmResetPassword(ConfirmResetPasswordRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->view(
                "resetPasswordChallenge",
                model: ResetPasswordChallenge::withErrors($errors, $request->token)
            );
        }

        try {
            $this->identityManager->resetPasswordFromToken($request->token, $request->newPassword);
        } catch (ResetPasswordChallengeException) {
            return $this->view(
                "resetPasswordChallenge",
                model: ResetPasswordChallenge::withErrors([
                    'token' => '{{accounts.reset-password-challenge.form.token.error.expired}}',
                ], $request->token)
            );
        } catch (UserIsNotActiveException) {
            return $this->view(
                "resetPasswordChallenge",
                model: ResetPasswordChallenge::withErrors([
                    'token' => '{{accounts.reset-password-challenge.form.token.error.invalid}}',
                ], $request->token)
            );
        }

        return $this->redirectToAction("signIn");
    }

    private function isUserAuthenticated(): bool
    {
        return $this->requestContext->getIdentity()->isAuthenticated();
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/accounts/sign-in'),
                controller: AccountsController::class,
                action: 'signIn'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/sign-in'),
                AccountsController::class,
                'signInUser'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/sign-up'),
                AccountsController::class,
                'signUp'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/sign-up'),
                AccountsController::class,
                'signUpUser'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/activate'),
                AccountsController::class,
                'activateUser'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/sign-out'),
                AccountsController::class,
                'signOut',
                authRequired: true
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/reset-password'),
                AccountsController::class,
                'resetPassword'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/reset-password'),
                AccountsController::class,
                'sendResetPasswordEmail'
            ),
            Route::create(
                RouteMethod::Get,
                Path::create('/accounts/reset-password-challenge'),
                AccountsController::class,
                'resetPasswordChallenge'
            ),
            Route::create(
                RouteMethod::Post,
                Path::create('/accounts/reset-password-challenge'),
                AccountsController::class,
                'confirmResetPassword',
            ),
        ];
    }
}
