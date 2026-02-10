<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Mailer;

use Infrastructure\Ports\BackgroundTasks\Settings\ChallengeEmailSettings;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PhpMailerException;

final class PhpMailerMailer implements MailerInterface
{
    public function __construct(
        private readonly ChallengeEmailSettings $settings,
    ) {
    }

    public function send(string $to, string $subject, string $htmlBody): void
    {
        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = $this->settings->host;
            $mailer->Port = $this->settings->port;
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->settings->username;
            $mailer->Password = $this->settings->password;
            if ($this->settings->encryption !== '') {
                $mailer->SMTPSecure = $this->settings->encryption;
            }

            $mailer->setFrom($this->settings->fromAddress, $this->settings->fromName);
            $mailer->addAddress($to);

            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $htmlBody;

            $mailer->send();
        } catch (PhpMailerException $exception) {
            throw new \RuntimeException(
                sprintf('Failed to send email to "%s": %s', $to, $exception->getMessage()),
                previous: $exception
            );
        }
    }
}
