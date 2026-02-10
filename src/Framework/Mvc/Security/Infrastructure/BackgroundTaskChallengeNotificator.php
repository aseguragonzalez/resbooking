<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Infrastructure;

use Framework\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTaskCommand;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Services\ChallengeNotificator;

final readonly class BackgroundTaskChallengeNotificator implements ChallengeNotificator
{
    public function __construct(
        private RegisterTask $registerTask,
    ) {
    }

    public function sendSignUpChallenge(string $email, SignUpChallenge $challenge): void
    {
        $command = new RegisterTaskCommand(
            taskType: 'send_sign_up_challenge_email',
            arguments: [
                'email' => $email,
                'type' => 'sign_up',
                'token' => $challenge->getToken(),
                'expiresAt' => $challenge->expiresAt->format('c'),
            ],
        );

        $this->registerTask->execute($command);
    }

    public function sendResetPasswordChallenge(string $email, ResetPasswordChallenge $challenge): void
    {
        $command = new RegisterTaskCommand(
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => $email,
                'type' => 'reset_password',
                'token' => $challenge->getToken(),
                'expiresAt' => $challenge->expiresAt->format('c'),
            ],
        );

        $this->registerTask->execute($command);
    }
}
