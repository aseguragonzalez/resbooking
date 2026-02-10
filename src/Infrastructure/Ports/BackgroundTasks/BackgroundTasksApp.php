<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks;

use DI\Container;
use Framework\BackgroundTasks\BackgroundTasksApp as FrameworkBackgroundTasksApp;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendResetPasswordChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendSignUpChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Mailer\PhpMailerMailer;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;

final class BackgroundTasksApp extends FrameworkBackgroundTasksApp
{
    public function __construct(Container $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    protected function configureSettings(): void
    {
        parent::configureSettings();

        $templateBasePath = getenv('EMAIL_TEMPLATES_PATH') ?: __DIR__ . '/../../../resources/email';
        $host = getenv('SMTP_HOST') ?: 'localhost';
        $port = (int) (getenv('SMTP_PORT') ?: 587);
        $username = getenv('SMTP_USER') ?: '';
        $password = getenv('SMTP_PASSWORD') ?: '';
        $encryption = getenv('SMTP_ENCRYPTION') ?: 'tls';
        $fromAddress = getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@example.com';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'Reservations';
        $appBaseUrl = getenv('APP_BASE_URL') ?: 'http://localhost';

        $this->container->set(
            ChallengeEmailSettings::class,
            new ChallengeEmailSettings(
                templateBasePath: $templateBasePath,
                host: $host,
                port: $port,
                username: $username,
                password: $password,
                encryption: $encryption,
                fromAddress: $fromAddress,
                fromName: $fromName,
                appBaseUrl: $appBaseUrl,
            )
        );

        $this->container->set(
            MailerInterface::class,
            $this->container->get(PhpMailerMailer::class)
        );
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
