<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Infrastructure;

use Framework\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTaskCommand;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Infrastructure\BackgroundTaskChallengeNotificator;
use PHPUnit\Framework\TestCase;

final class BackgroundTaskChallengeNotificatorTest extends TestCase
{
    public function testSendSignUpChallengeRegistersBackgroundTaskWithExpectedPayload(): void
    {
        $userIdentity = UserIdentity::new('user@example.com', ['admin'], 'password');
        $expiresAt = new \DateTimeImmutable('2024-01-01T12:00:00+00:00');
        $challenge = SignUpChallenge::build('sign-up-token', $expiresAt, $userIdentity);

        $registerTask = $this->createMock(RegisterTask::class);
        $registerTask
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(
                function (RegisterTaskCommand $command) use ($expiresAt): bool {
                    $this->assertSame('send_sign_up_challenge_email', $command->taskType);
                    $this->assertSame('user@example.com', $command->arguments['email']);
                    $this->assertSame('sign_up', $command->arguments['type']);
                    $this->assertSame('sign-up-token', $command->arguments['token']);
                    $this->assertSame($expiresAt->format('c'), $command->arguments['expiresAt']);

                    return true;
                }
            ));

        $notificator = new BackgroundTaskChallengeNotificator($registerTask);

        $notificator->sendSignUpChallenge('user@example.com', $challenge);
    }

    public function testSendResetPasswordChallengeRegistersBackgroundTaskWithExpectedPayload(): void
    {
        $userIdentity = UserIdentity::new('user@example.com', ['admin'], 'password');
        $expiresAt = new \DateTimeImmutable('2024-01-02T15:30:00+00:00');
        $challenge = ResetPasswordChallenge::build('reset-token', $expiresAt, $userIdentity);

        $registerTask = $this->createMock(RegisterTask::class);
        $registerTask
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(
                function (RegisterTaskCommand $command) use ($expiresAt): bool {
                    $this->assertSame('send_reset_password_challenge_email', $command->taskType);
                    $this->assertSame('user@example.com', $command->arguments['email']);
                    $this->assertSame('reset_password', $command->arguments['type']);
                    $this->assertSame('reset-token', $command->arguments['token']);
                    $this->assertSame($expiresAt->format('c'), $command->arguments['expiresAt']);

                    return true;
                }
            ));

        $notificator = new BackgroundTaskChallengeNotificator($registerTask);

        $notificator->sendResetPasswordChallenge('user@example.com', $challenge);
    }
}
