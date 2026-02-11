<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks;

use DI\Container;
use Framework\BackgroundTasks\Domain\TemplateEngine;
use Framework\BackgroundTasks\BaseBackgroundTasksApp;
use Framework\Files\DefaultFileManager;
use Framework\Files\FileManager;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendResetPasswordChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendSignUpChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Mailer\PhpMailerMailer;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;

final class BackgroundTasksApp extends BaseBackgroundTasksApp
{
    public function __construct(Container $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    protected function configure(): void
    {
        $this->container->set(
            ChallengeEmailSettings::class,
            new ChallengeEmailSettings(
                templateBasePath: $this->basePath . (getenv('EMAIL_TEMPLATES_PATH') ?: '/assets/templates'),
                host: getenv('SMTP_HOST') ?: 'smtp4dev',
                port: (int) (getenv('SMTP_PORT') ?: 25),
                username: getenv('SMTP_USER') ?: '',
                password: getenv('SMTP_PASSWORD') ?: '',
                encryption: getenv('SMTP_ENCRYPTION') ?: '',
                fromAddress: getenv('MAIL_FROM_ADDRESS') ?: '',
                fromName: getenv('MAIL_FROM_NAME') ?: '',
                appBaseUrl: getenv('APP_BASE_URL') ?: '',
            )
        );

        $this->container->set(FileManager::class, $this->container->get(DefaultFileManager::class));
        $this->container->set(TemplateEngine::class, new TemplateEngine());
        $this->container->set(MailerInterface::class, $this->container->get(PhpMailerMailer::class));
    }

    /**
     * @return array<string, string>
     */
    protected function getHandlerMap(): array
    {
        return [
            'send_sign_up_challenge_email' => SendSignUpChallengeEmailHandler::class,
            'send_reset_password_challenge_email' => SendResetPasswordChallengeEmailHandler::class,
        ];
    }
}
