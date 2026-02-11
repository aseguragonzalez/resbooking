<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Mailer;

interface MailerInterface
{
    public function send(string $to, string $subject, string $htmlBody): void;
}
