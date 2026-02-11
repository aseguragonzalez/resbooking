<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\Task;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendResetPasswordChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use PHPUnit\Framework\TestCase;

final class SendResetPasswordChallengeEmailHandlerTest extends TestCase
{
    public function testHandleSendsEmailWithExpectedContent(): void
    {
        $templateDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'email_templates_' . uniqid();
        mkdir($templateDir);

        $templatePath = $templateDir . DIRECTORY_SEPARATOR . 'reset_password_challenge.html';
        file_put_contents(
            $templatePath,
            'Link: {{resetLink}}, Token: {{token}}, Expires: {{expiresAt}}, Email: {{email}}'
        );

        $settings = new ChallengeEmailSettings(
            templateBasePath: $templateDir,
            host: 'localhost',
            port: 587,
            username: 'user',
            password: 'pass',
            encryption: 'tls',
            fromAddress: 'no-reply@example.com',
            fromName: 'Reservations',
            appBaseUrl: 'https://example.com',
        );

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->with(
                'user@example.com',
                'Reset your password',
                $this->callback(function (string $body): bool {
                    return str_contains(
                        $body,
                        'Link: https://example.com/accounts/reset-password-challenge?token=token-456'
                    )
                        && str_contains($body, 'Token: token-456')
                        && str_contains($body, 'Expires: 2024-01-02T15:30:00+00:00')
                        && str_contains($body, 'Email: user@example.com');
                })
            );

        $handler = new SendResetPasswordChallengeEmailHandler($settings, $mailer);

        $task = Task::build(
            id: 'task-2',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => 'user@example.com',
                'token' => 'token-456',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $handler->handle($task);
    }

    public function testHandleThrowsWhenTaskHasInvalidArguments(): void
    {
        $templateDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'email_templates_' . uniqid();
        mkdir($templateDir);
        $templatePath = $templateDir . DIRECTORY_SEPARATOR . 'reset_password_challenge.html';
        file_put_contents($templatePath, '{{email}}');

        $settings = new ChallengeEmailSettings(
            templateBasePath: $templateDir,
            host: 'localhost',
            port: 587,
            username: 'user',
            password: 'pass',
            encryption: 'tls',
            fromAddress: 'no-reply@example.com',
            fromName: 'Reservations',
            appBaseUrl: 'https://example.com',
        );

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->never())->method('send');

        $handler = new SendResetPasswordChallengeEmailHandler($settings, $mailer);

        $task = Task::build(
            id: 'task-invalid',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => '',
                'token' => 'token-456',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for reset-password challenge email. Task id: "task-invalid", invalid: email'
        );

        $handler->handle($task);
    }
}
