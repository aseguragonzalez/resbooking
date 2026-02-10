<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;

final readonly class SendResetPasswordChallengeEmailHandler implements TaskHandler
{
    public function __construct(
        private ChallengeEmailSettings $settings,
        private MailerInterface $mailer,
    ) {
    }

    public function handle(Task $task): void
    {
        $email = $task->arguments['email'] ?? null;
        $token = $task->arguments['token'] ?? null;
        $expiresAt = $task->arguments['expiresAt'] ?? null;

        $invalidArguments = [];

        if (!is_string($email) || $email === '') {
            $invalidArguments[] = 'email';
        }

        if (!is_string($token) || $token === '') {
            $invalidArguments[] = 'token';
        }

        if ($invalidArguments !== []) {
            $taskId = $task->id ?? 'n/a';
            $taskType = $task->type ?? 'n/a';

            throw new \InvalidArgumentException(sprintf(
                'Invalid task arguments for reset-password challenge email. Task id: "%s", type: "%s", invalid: %s',
                (string) $taskId,
                (string) $taskType,
                implode(', ', $invalidArguments)
            ));
        }

        if (!is_string($expiresAt) || $expiresAt === '') {
            $taskId = $task->id ?? 'n/a';
            $taskType = $task->type ?? 'n/a';

            throw new \InvalidArgumentException(sprintf(
                'Missing or invalid "expiresAt" in task arguments for reset-password challenge email. Task id: "%s", type: "%s"',
                (string) $taskId,
                (string) $taskType
            ));
        }

        $resetLink = rtrim($this->settings->appBaseUrl, '/') .
            '/accounts/reset-password-challenge?token=' . urlencode($token);

        $templatePath = rtrim($this->settings->templateBasePath, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . 'reset_password_challenge.html';

        if (!is_file($templatePath)) {
            throw new \RuntimeException(sprintf('Email template not found at path "%s"', $templatePath));
        }

        $template = file_get_contents($templatePath);
        if ($template === false) {
            throw new \RuntimeException(sprintf('Failed to read email template at path "%s"', $templatePath));
        }

        $body = str_replace(
            ['{{resetLink}}', '{{token}}', '{{expiresAt}}', '{{email}}'],
            [$resetLink, $token, $expiresAt, $email],
            $template
        );

        $subject = 'Reset your password';

        $this->mailer->send($email, $subject, $body);
    }
}
