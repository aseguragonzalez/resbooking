<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use Infrastructure\Ports\BackgroundTasks\Tasks\SignUpChallengeEmailTask;

final readonly class SendSignUpChallengeEmailHandler implements TaskHandler
{
    public function __construct(
        private ChallengeEmailSettings $settings,
        private MailerInterface $mailer,
    ) {
    }

    public function handle(Task $task): void
    {
        $signUpTask = SignUpChallengeEmailTask::fromTask($task);

        $activationLink = rtrim($this->settings->appBaseUrl, '/') .
            '/accounts/activate?token=' . urlencode($signUpTask->getToken());

        $templatePath = rtrim($this->settings->templateBasePath, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . 'sign_up_challenge.html';

        if (!is_file($templatePath)) {
            throw new \RuntimeException(sprintf('Email template not found at path "%s"', $templatePath));
        }

        $template = file_get_contents($templatePath);
        if ($template === false) {
            throw new \RuntimeException(sprintf('Failed to read email template at path "%s"', $templatePath));
        }

        $escapedActivationLink = htmlspecialchars($activationLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escapedToken = htmlspecialchars($signUpTask->getToken(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escapedExpiresAt = htmlspecialchars($signUpTask->getExpiresAt(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escapedEmail = htmlspecialchars($signUpTask->getEmail(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $body = str_replace(
            ['{{activationLink}}', '{{token}}', '{{expiresAt}}', '{{email}}'],
            [$escapedActivationLink, $escapedToken, $escapedExpiresAt, $escapedEmail],
            $template
        );

        $subject = 'Activate your account';

        $this->mailer->send($signUpTask->getEmail(), $subject, $body);
    }
}
