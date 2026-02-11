<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\TemplateEngine;
use Framework\BackgroundTasks\Domain\Task;
use Framework\Files\FileManager;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendSignUpChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use PHPUnit\Framework\TestCase;

final class SendSignUpChallengeEmailHandlerTest extends TestCase
{
    public function testHandleSendsEmailWithExpectedContent(): void
    {
        $templateContent = 'Link: {{activationLink}}, Token: {{token}}, Expires: {{expiresAt}}, Email: {{email}}';

        $fileManager = $this->createStub(FileManager::class);
        $fileManager->method('readTextPlain')->willReturn($templateContent);

        $templateDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'email_templates_' . uniqid();
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

        $templateEngine = new TemplateEngine();
        $handler = new SendSignUpChallengeEmailHandler(
            $settings,
            $mailer,
            $fileManager,
            $templateEngine
        );

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
    }

    public function testHandleThrowsWhenTaskHasInvalidArguments(): void
    {
        $fileManager = $this->createStub(FileManager::class);
        $fileManager->method('readTextPlain')->willReturn('{{email}}');

        $templateDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'email_templates_' . uniqid();
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

        $templateEngine = new TemplateEngine();
        $handler = new SendSignUpChallengeEmailHandler(
            $settings,
            $mailer,
            $fileManager,
            $templateEngine
        );

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
