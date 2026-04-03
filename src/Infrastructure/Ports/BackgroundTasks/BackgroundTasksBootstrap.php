<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks;

use DI\Container;
use Framework\Files\DefaultFileManager;
use Framework\Files\FileManager;
use Framework\Mvc\LoggerSettings;
use Framework\Mvc\BackgroundTasks\BackgroundTasksRuntime;
use Framework\Mvc\BackgroundTasks\BackgroundTasksSettings;
use Framework\Mvc\BackgroundTasks\Domain\TemplateEngine;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendResetPasswordChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Handlers\SendSignUpChallengeEmailHandler;
use Infrastructure\Ports\BackgroundTasks\Mailer\MailerInterface;
use Infrastructure\Ports\BackgroundTasks\Mailer\PhpMailerMailer;
use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

final class BackgroundTasksBootstrap
{
    public static function register(Container $container, string $basePath): void
    {
        $handlerMap = [
            'send_sign_up_challenge_email' => SendSignUpChallengeEmailHandler::class,
            'send_reset_password_challenge_email' => SendResetPasswordChallengeEmailHandler::class,
        ];

        $container->set(
            LoggerSettings::class,
            new LoggerSettings(
                environment: getenv('ENVIRONMENT') ?: 'local',
                serviceName: getenv('BACKGROUND_TASKS_SERVICE_NAME') ?: 'background-tasks',
                serviceVersion: getenv('BACKGROUND_TASKS_SERVICE_VERSION') ?: '1.0.0',
                logLevel: getenv('BACKGROUND_TASKS_LOG_LEVEL') ?: 'debug',
            ),
        );
        $container->set(
            BackgroundTasksSettings::class,
            new BackgroundTasksSettings(
                host: getenv('BACKGROUND_TASKS_DATABASE_HOST') ?: 'localhost',
                database: getenv('BACKGROUND_TASKS_DATABASE_NAME') ?: 'reservations',
                user: getenv('BACKGROUND_TASKS_DATABASE_USER') ?: 'root',
                password: getenv('BACKGROUND_TASKS_DATABASE_PASSWORD') ?: '',
                handlerMap: $handlerMap,
            ),
        );

        /** @var LoggerSettings $loggerSettings */
        $loggerSettings = $container->get(LoggerSettings::class);

        $handler = new StreamHandler(
            stream: 'php://stdout',
            level: self::logLevelFromSettings($loggerSettings)
        );
        $handler->setFormatter(new LineFormatter(
            format: '[%datetime%] %level_name%: %message%',
            dateFormat: 'Y-m-d H:i:s',
            allowInlineLineBreaks: true,
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));

        $logger = new Logger($loggerSettings->serviceName);
        $logger->pushHandler($handler);
        $logger->pushProcessor(new PsrLogMessageProcessor());

        $container->set(LoggerInterface::class, $logger);

        $container->set(
            ChallengeEmailSettings::class,
            new ChallengeEmailSettings(
                templateBasePath: $basePath . (getenv('EMAIL_TEMPLATES_PATH') ?: '/assets/templates'),
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

        $container->set(FileManager::class, $container->get(DefaultFileManager::class));
        $container->set(TemplateEngine::class, new TemplateEngine());
        $container->set(MailerInterface::class, $container->get(PhpMailerMailer::class));

        BackgroundTasksRuntime::register($container);
    }

    private static function logLevelFromSettings(LoggerSettings $loggerSettings): Level
    {
        $logLevel = $loggerSettings->logLevel;
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if (!in_array($logLevel, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$logLevel}");
        }

        return Level::fromName($logLevel);
    }
}
