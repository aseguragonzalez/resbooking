<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Application\Projects\CreateNewProject\CreateNewProject;
use PHPUnit\Framework\TestCase;
use Infrastructure\Ports\Dashboard\Controllers\AccountsController;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\SignIn;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\SignUp;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPassword;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPasswordChallenge;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignInRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignUpRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ResetPasswordRequest;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ConfirmResetPasswordRequest;
use Seedwork\Infrastructure\Mvc\Security\IdentityManager;
use Seedwork\Infrastructure\Mvc\Settings;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\UserIsNotActiveException;
use PHPUnit\Framework\MockObject\MockObject;
use Seedwork\Infrastructure\Mvc\Security\Challenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\InvalidCredentialsException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\SignUpChallengeException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\ResetPasswordChallengeException;
use Seedwork\Infrastructure\Mvc\Actions\Responses\LocalRedirectTo;
use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Faker\Factory;
use Faker\Generator;

final class AccountsControllerTest extends TestCase
{
    private CreateNewProject&MockObject $createNewProject;
    private IdentityManager&MockObject $identityManager;
    private Settings $settings;
    private AccountsController $controller;
    private Generator $faker;

    protected function setUp(): void
    {
        $this->createNewProject = $this->createMock(CreateNewProject::class);
        $this->identityManager = $this->createMock(IdentityManager::class);
        $this->settings = new Settings(basePath: '/');
        $this->controller = new AccountsController($this->createNewProject, $this->identityManager, $this->settings);
        $this->faker = Factory::create();
    }

    public function testSignInReturnsSignInView(): void
    {
        $response = $this->controller->signIn();
        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(SignIn::class, $response->data);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/signIn', $view->viewPath);
    }

    public function testSignUpReturnsSignUpView(): void
    {
        $response = $this->controller->signUp();
        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(SignUp::class, $response->data);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/signUp', $view->viewPath);
    }

    public function testResetPasswordReturnsResetPasswordView(): void
    {
        $response = $this->controller->resetPassword();
        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(ResetPassword::class, $response->data);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/resetPassword', $view->viewPath);
    }

    public function testResetPasswordChallengeReturnsChallengeView(): void
    {
        $token = $this->faker->uuid();
        $response = $this->controller->resetPasswordChallenge($token);
        $this->assertInstanceOf(ResetPasswordChallenge::class, $response->data);
        $this->assertSame($token, $response->data->token);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/resetPasswordChallenge', $view->viewPath);
    }

    public function testSignInUserWithValidationErrors(): void
    {
        $request = new SignInRequest(
            username: '',
            password: '',
            rememberMe: ''
        );
        $response = $this->controller->signInUser($request);
        $this->assertInstanceOf(SignIn::class, $response->data);
        /** @var SignIn $data */
        $data = $response->data;
        $this->assertTrue(count($data->errorSummary) > 0);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/signIn', $view->viewPath);
    }

    public function testSignInUserWithInvalidCredentials(): void
    {
        $request = new SignInRequest(
            username: $this->faker->userName(),
            password: '@Home1234',
            rememberMe: 'off'
        );
        $this->identityManager->method('signIn')->willThrowException(new InvalidCredentialsException());
        $response = $this->controller->signInUser($request);

        $this->assertInstanceOf(SignIn::class, $response->data);
        /** @var SignIn $data */
        $data = $response->data;
        $this->assertTrue(count($data->errorSummary) > 0);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/signIn', $view->viewPath);
    }

    public function testSignInUser(): void
    {
        $request = new SignInRequest(
            username: $this->faker->email(),
            password: '@Home1234',
            rememberMe: 'off'
        );
        $challenge = $this->createMock(Challenge::class);
        $challenge->method('getExpiresAt')->willReturn(new \DateTimeImmutable('+1 hour'));
        $challenge->method('getToken')->willReturn($this->faker->uuid());
        $this->identityManager->method('signIn')->willReturn($challenge);

        $response = $this->controller->signInUser($request);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $data */
        $data = $response;
        $this->assertEquals('index', $data->action);
        $this->assertEquals('Infrastructure\Ports\Dashboard\Controllers\DashboardController', $data->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testSignUpUserWithValidationErrors(): void
    {
        $request = new SignUpRequest(
            username: '',
            password: '',
            passwordConfirm: '',
            agree: ''
        );
        $response = $this->controller->signUpUser($request);

        $this->assertInstanceOf(SignUp::class, $response->data);
        /** @var SignUp $data */
        $data = $response->data;
        $this->assertTrue(count($data->errorSummary) > 0);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/signUp', $view->viewPath);
    }

    public function testSignUpUser(): void
    {
        $password = '@Home1234';
        $request = new SignUpRequest(
            username: $this->faker->email(),
            password: $password,
            passwordConfirm: $password,
            agree: 'on'
        );
        $this->identityManager->expects($this->once())->method('signUp');
        $this->createNewProject->expects($this->once())->method('execute');

        $response = $this->controller->signUpUser($request);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $data */
        $data = $response;
        $this->assertEquals('signIn', $data->action);
        $this->assertEquals('Infrastructure\Ports\Dashboard\Controllers\AccountsController', $data->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testActivateUser(): void
    {
        $token = $this->faker->uuid();
        $this->identityManager->expects($this->once())->method('activateUserIdentity');

        $response = $this->controller->activateUser($token);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $data */
        $data = $response;
        $this->assertEquals('signIn', $data->action);
        $this->assertEquals('Infrastructure\Ports\Dashboard\Controllers\AccountsController', $data->controller);
    }

    public function testActivateUserWithException(): void
    {
        $token = $this->faker->uuid();
        $this->identityManager->method('activateUserIdentity')->willThrowException(new SignUpChallengeException());

        $response = $this->controller->activateUser($token);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Accounts/tokenExpired', $view->viewPath);
    }

    public function testSendResetPasswordEmailWithValidationErrors(): void
    {
        $request = new ResetPasswordRequest(username: '');
        $response = $this->controller->sendResetPasswordEmail($request);

        $this->assertInstanceOf(ResetPassword::class, $response->data);
        /** @var ResetPassword $data */
        $data = $response->data;
        $this->assertTrue(count($data->errorSummary) > 0);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/resetPassword', $view->viewPath);
    }

    public function testSendResetPasswordEmail(): void
    {
        $request = new ResetPasswordRequest(username: $this->faker->email());
        $this->identityManager->expects($this->once())->method('resetPasswordChallenge');

        $response = $this->controller->sendResetPasswordEmail($request);

        $this->assertInstanceOf(ResetPassword::class, $response->data);
        /** @var ResetPassword $data */
        $data = $response->data;
        $this->assertTrue($data->hasSucceeded);
        $this->assertTrue(count($data->errorSummary) === 0);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/resetPassword', $view->viewPath);
    }

    public function testConfirmResetPassword(): void
    {
        $request = new ConfirmResetPasswordRequest(
            token: $this->faker->uuid(),
            newPassword: '@Home1234'
        );
        $this->identityManager->expects($this->once())
            ->method('resetPasswordFromToken')
            ->with($request->token, $request->newPassword);

        $response = $this->controller->confirmResetPassword($request);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $data */
        $data = $response;
        $this->assertEquals('signIn', $data->action);
        $this->assertEquals('Infrastructure\Ports\Dashboard\Controllers\AccountsController', $data->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testConfirmResetPasswordWithValidationErrors(): void
    {
        $request = new ConfirmResetPasswordRequest(token: '', newPassword: '');
        $response = $this->controller->confirmResetPassword($request);

        $this->assertInstanceOf(ResetPasswordChallenge::class, $response->data);
        /** @var ResetPasswordChallenge $data */
        $data = $response->data;
        $this->assertTrue(count($data->errorSummary) > 0);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/resetPasswordChallenge', $view->viewPath);
    }

    public function testConfirmResetPasswordWithChallengeException(): void
    {
        $request = new ConfirmResetPasswordRequest(token: $this->faker->uuid(), newPassword: '@Home1234');
        $this->identityManager
            ->method('resetPasswordFromToken')
            ->willThrowException(new ResetPasswordChallengeException());

        $response = $this->controller->confirmResetPassword($request);

        $this->assertInstanceOf(ResetPasswordChallenge::class, $response->data);
        /** @var ResetPasswordChallenge $data */
        $data = $response->data;
        $this->assertTrue(count($data->errorSummary) > 0);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/resetPasswordChallenge', $view->viewPath);
    }

    public function testGetRoutesConfiguration(): void
    {
        $routes = AccountsController::getRoutes();

        $this->assertCount(10, $routes);
        $expected = [
            ['Get', '/accounts/sign-in', 'signIn', false],
            ['Post', '/accounts/sign-in', 'signInUser', false],
            ['Get', '/accounts/sign-up', 'signUp', false],
            ['Post', '/accounts/sign-up', 'signUpUser', false],
            ['Get', '/accounts/activate', 'activateUser', false],
            ['Get', '/accounts/sign-out', 'signOut', true],
            ['Get', '/accounts/reset-password', 'resetPassword', false],
            ['Post', '/accounts/reset-password', 'sendResetPasswordEmail', false],
            ['Get', '/accounts/reset-password-challenge', 'resetPasswordChallenge', false],
            ['Post', '/accounts/reset-password-challenge', 'confirmResetPassword', false],
        ];
        foreach ($expected as $index => [$method, $path, $action, $authRequired]) {
            $route = $routes[$index];
            $this->assertEquals($method, $route->method->name);
            $this->assertEquals($path, $route->path->value());
            $this->assertEquals(AccountsController::class, $route->controller);
            $this->assertEquals($action, $route->action);
        }
    }

    public function testSignOutWithNoTokenRedirectsToSignIn(): void
    {
        $request = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $request->method('getCookieParams')->willReturn([]);
        $response = $this->controller->signOut($request);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $data */
        $data = $response;
        $this->assertEquals('signIn', $data->action);
        $this->assertEquals('Infrastructure\Ports\Dashboard\Controllers\AccountsController', $data->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testSignOutWithValidTokenSignsOutAndRedirects(): void
    {
        $token = $this->faker->uuid();
        $request = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $request->method('getCookieParams')->willReturn(['auth' => $token]);
        $this->identityManager->expects($this->once())->method('signOut')->with($token);

        $response = $this->controller->signOut($request);

        $this->assertInstanceOf(LocalRedirectTo::class, $response);
        /** @var LocalRedirectTo $data */
        $data = $response;
        $this->assertEquals('signIn', $data->action);
        $this->assertEquals('Infrastructure\Ports\Dashboard\Controllers\AccountsController', $data->controller);
        $this->assertEquals(303, $response->statusCode->value);
    }

    public function testSignInUserThrowsInvalidCredentialsException(): void
    {
        $request = new SignInRequest(
            username: $this->faker->email(),
            password: '@WrongPassword',
            rememberMe: 'off'
        );
        $this->identityManager->method('signIn')->willThrowException(new InvalidCredentialsException());

        $response = $this->controller->signInUser($request);

        $this->assertInstanceOf(SignIn::class, $response->data);
        /** @var SignIn $data */
        $data = $response->data;
        $this->assertEquals('username', $data->errorSummary[0]->field);
        $this->assertEquals('{{accounts.signin.form.invalid-credentials}}', $data->errorSummary[0]->message);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/signIn', $view->viewPath);
    }

    public function testSignInUserThrowsUserIsNotActiveException(): void
    {
        $request = new SignInRequest(
            username: $this->faker->email(),
            password: '@Home1234',
            rememberMe: 'off'
        );
        $this->identityManager->method('signIn')->willThrowException(new UserIsNotActiveException());

        $response = $this->controller->signInUser($request);

        $this->assertInstanceOf(SignIn::class, $response->data);
        /** @var SignIn $data */
        $data = $response->data;
        $this->assertEquals('username-inactive', $data->errorSummary[0]->field);
        $this->assertEquals('{{accounts.signin.form.inactive-user}}', $data->errorSummary[0]->message);
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/signIn', $view->viewPath);
    }

    public function testConfirmResetPasswordWithUserIsNotActiveException(): void
    {
        $request = new ConfirmResetPasswordRequest(
            token: $this->faker->uuid(),
            newPassword: '@Home1234'
        );
        $this->identityManager
            ->method('resetPasswordFromToken')
            ->willThrowException(new UserIsNotActiveException());

        $response = $this->controller->confirmResetPassword($request);

        $this->assertInstanceOf(ResetPasswordChallenge::class, $response->data);
        /** @var ResetPasswordChallenge $data */
        $data = $response->data;
        $this->assertTrue(count($data->errorSummary) > 0);
        $this->assertEquals('token', $data->errorSummary[0]->field);
        $this->assertEquals(
            '{{accounts.reset-password-challenge.form.token.error.invalid}}',
            $data->errorSummary[0]->message
        );
        $this->assertEquals(200, $response->statusCode->value);
        /** @var View $view */
        $view = $response;
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('Accounts/resetPasswordChallenge', $view->viewPath);
    }
}
