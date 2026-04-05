<?php

declare(strict_types=1);

namespace Infrastructure\Adapters;

use Framework\Apps\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\Apps\BackgroundTasks\Application\RegisterTask\RegisterTaskCommand;
use Framework\Module\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Module\Security\Domain\Entities\SignUpChallenge;
use Framework\Module\Security\Domain\Services\ChallengeNotificator;

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
                'token' => $challenge->getToken(),
                'expiresAt' => $challenge->expiresAt->format('c'),
            ],
        );

        $this->registerTask->execute($command);
    }
}
