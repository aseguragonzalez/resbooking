<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;

final readonly class SendSignUpChallengeEmailHandler implements TaskHandler
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

        if (!is_string($email) || $email === '' || !is_string($token) || $token === '') {
            throw new \InvalidArgumentException('Invalid task arguments for sign-up challenge email');
        }

        if (!is_string($expiresAt) || $expiresAt === '') {
            throw new \InvalidArgumentException('Missing expiresAt in task arguments for sign-up challenge email');
        }

        $activationLink = rtrim($this->settings->appBaseUrl, '/') .
            '/accounts/activate?token=' . urlencode($token);

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
        $escapedToken = htmlspecialchars($token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escapedExpiresAt = htmlspecialchars($expiresAt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escapedEmail = htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $body = str_replace(
            ['{{activationLink}}', '{{token}}', '{{expiresAt}}', '{{email}}'],
            [$escapedActivationLink, $escapedToken, $escapedExpiresAt, $escapedEmail],
            $template
        );

        $subject = 'Activate your account';

        $this->mailer->send($email, $subject, $body);
    }
}
