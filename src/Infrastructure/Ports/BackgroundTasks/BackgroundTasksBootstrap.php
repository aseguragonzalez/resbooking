<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks;

use DI\Container;
use Framework\Mvc\Files\DefaultFileManager;
use Framework\Mvc\Files\FileManager;
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
            BackgroundTasksSettings::class,
            new BackgroundTasksSettings(
                host: getenv('BACKGROUND_TASKS_DATABASE_HOST') ?: 'localhost',
                database: getenv('BACKGROUND_TASKS_DATABASE_NAME') ?: 'reservations',
                user: getenv('BACKGROUND_TASKS_DATABASE_USER') ?: 'root',
                password: getenv('BACKGROUND_TASKS_DATABASE_PASSWORD') ?: '',
                handlerMap: $handlerMap,
            ),
        );

        $serviceName = getenv('BACKGROUND_TASKS_SERVICE_NAME') ?: 'background-tasks';
        $logLevel = getenv('BACKGROUND_TASKS_LOG_LEVEL') ?: 'debug';

        $handler = new StreamHandler(
            stream: 'php://stdout',
            level: self::logLevelFromSettings($logLevel)
        );
        $handler->setFormatter(new LineFormatter(
            format: '[%datetime%] %level_name%: %message%',
            dateFormat: 'Y-m-d H:i:s',
            allowInlineLineBreaks: true,
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));

        $logger = new Logger($serviceName);
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

    private static function logLevelFromSettings(string $logLevel): Level
    {
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $normalized = strtolower($logLevel);
        if (!in_array($normalized, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$logLevel}");
        }

        return Level::fromName($normalized);
    }
}
