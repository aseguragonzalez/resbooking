<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\Task;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendSignUpChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use PHPUnit\Framework\TestCase;

final class SendSignUpChallengeEmailHandlerTest extends TestCase
{
    public function testHandleSendsEmailWithExpectedContent(): void
    {
        $templateDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'email_templates_' . uniqid();
        $this->assertTrue(mkdir($templateDir));

        $templatePath = $templateDir . DIRECTORY_SEPARATOR . 'sign_up_challenge.html';

        try {
            $this->assertNotFalse(
                file_put_contents(
                    $templatePath,
                    'Link: {{activationLink}}, Token: {{token}}, Expires: {{expiresAt}}, Email: {{email}}'
                )
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
                    'Activate your account',
                    $this->callback(function (string $body): bool {
                        return str_contains($body, 'Link: https://example.com/accounts/activate?token=token-123')
                            && str_contains($body, 'Token: token-123')
                            && str_contains($body, 'Expires: 2024-01-01T12:00:00+00:00')
                            && str_contains($body, 'Email: user@example.com');
                    })
                );

            $handler = new SendSignUpChallengeEmailHandler($settings, $mailer);

            $task = Task::build(
                id: 'task-1',
                taskType: 'send_sign_up_challenge_email',
                arguments: [
                    'email' => 'user@example.com',
                    'token' => 'token-123',
                    'expiresAt' => '2024-01-01T12:00:00+00:00',
                ]
            );

            $handler->handle($task);
        } finally {
            if (is_file($templatePath)) {
                @unlink($templatePath);
            }

            if (is_dir($templateDir)) {
                @rmdir($templateDir);
            }
        }
    }

    public function testHandleThrowsWhenTaskHasInvalidArguments(): void
    {
        $templateDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'email_templates_' . uniqid();
        mkdir($templateDir);
        $templatePath = $templateDir . DIRECTORY_SEPARATOR . 'sign_up_challenge.html';
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

        $handler = new SendSignUpChallengeEmailHandler($settings, $mailer);

        $task = Task::build(
            id: 'task-invalid',
            taskType: 'send_sign_up_challenge_email',
            arguments: [
                'email' => '',
                'token' => 'token-456',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for sign-up challenge email. Task id: "task-invalid", invalid: email'
        );

        $handler->handle($task);
    }
}
