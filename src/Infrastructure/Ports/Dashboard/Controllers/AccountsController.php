<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPassword;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPasswordChallenge;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\SignIn;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\SignUp;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ConfirmResetPasswordRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ResetPasswordRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignInRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignUpRequest;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Responses\Headers\SetCookie;
use Seedwork\Infrastructure\Mvc\Security\IdentityManager;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\InvalidCredentialsException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\ResetPasswordChallengeException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\SignUpChallengeException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\UserIsNotActiveException;
use Seedwork\Infrastructure\Mvc\Settings;

final class AccountsController extends Controller
{
    public function __construct(
        private readonly IdentityManager $identityManager,
        private readonly Settings $settings
    ) {
    }

    public function signIn(): ActionResponse
    {
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
            $this->addHeader(new SetCookie(
                cookieName: $this->settings->authCookieName,
                cookieValue: $challenge->getToken(),
                expires: $challenge->getExpiresAt()->getTimestamp(),
                httpOnly: false, // TODO: set httpOnly true
                secure: false, // TODO: set secure true
                sameSite: 'Lax' // TODO: set strict
            ));
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
        $this->addHeader(new SetCookie(
            cookieName: $this->settings->authCookieName,
            cookieValue: '',
            httpOnly: false,
            secure: false,
            sameSite: 'Lax'
        ));
        return $this->redirectToAction("signIn");
    }

    public function signUp(): ActionResponse
    {
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
}
