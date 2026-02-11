<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Handlers;

use Framework\BackgroundTasks\Domain\TemplateEngine;
use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskHandler;
use Framework\Files\FileManager;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use Infrastructure\Ports\BackgroundTasks\Tasks\SignUpChallengeEmailTask;

final readonly class SendSignUpChallengeEmailHandler implements TaskHandler
{
    public function __construct(
        private ChallengeEmailSettings $settings,
        private MailerInterface $mailer,
        private FileManager $fileManager,
        private TemplateEngine $templateEngine,
    ) {
    }

    public function handle(Task $task): void
    {
        $signUpTask = SignUpChallengeEmailTask::fromTask($task);

        $activationLink = rtrim($this->settings->appBaseUrl, '/') .
            '/accounts/activate?token=' . urlencode($signUpTask->getToken());

        $templatePath = rtrim($this->settings->templateBasePath, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . 'sign_up_challenge.html';

        $template = $this->fileManager->readTextPlain($templatePath);

        $values = [
            'activationLink' => $activationLink,
            'token' => $signUpTask->getToken(),
            'expiresAt' => $signUpTask->getExpiresAt(),
            'email' => $signUpTask->getEmail(),
        ];
        $body = $this->templateEngine->render($template, $values);

        $subject = 'Activate your account';

        $this->mailer->send($signUpTask->getEmail(), $subject, $body);
    }
}
