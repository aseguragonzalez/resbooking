<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskHandler;
use Framework\Files\FileManager;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use Infrastructure\Ports\BackgroundTasks\Tasks\ResetPasswordChallengeEmailTask;

final readonly class SendResetPasswordChallengeEmailHandler implements TaskHandler
{
    public function __construct(
        private ChallengeEmailSettings $settings,
        private MailerInterface $mailer,
        private FileManager $fileManager,
    ) {
    }

    public function handle(Task $task): void
    {
        $resetTask = ResetPasswordChallengeEmailTask::fromTask($task);

        $resetLink = rtrim($this->settings->appBaseUrl, '/') .
            '/accounts/reset-password-challenge?token=' . urlencode($resetTask->getToken());

        $templatePath = rtrim($this->settings->templateBasePath, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . 'reset_password_challenge.html';

        $template = $this->fileManager->readTextPlain($templatePath);

        $body = str_replace(
            ['{{resetLink}}', '{{token}}', '{{expiresAt}}', '{{email}}'],
            [$resetLink, $resetTask->getToken(), $resetTask->getExpiresAt(), $resetTask->getEmail()],
            $template
        );

        $subject = 'Reset your password';

        $this->mailer->send($resetTask->getEmail(), $subject, $body);
    }
}
