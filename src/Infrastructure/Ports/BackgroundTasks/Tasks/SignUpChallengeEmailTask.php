<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Tasks;

use Framework\BackgroundTasks\Domain\Task;

final readonly class SignUpChallengeEmailTask extends Task
{
    private const TASK_TYPE = 'send_sign_up_challenge_email';

    /**
     * @param array<string, mixed> $arguments
     */
    protected function __construct(
        string $id,
        string $taskType,
        array $arguments = [],
        bool $processed = false,
        ?\DateTimeImmutable $processedAt = null,
    ) {
        parent::__construct($id, $taskType, $arguments, $processed, $processedAt);
    }

    public static function fromTask(Task $task): self
    {
        if ($task->taskType !== self::TASK_TYPE) {
            throw new \InvalidArgumentException(sprintf(
                'Expected task type "%s", got "%s". Task id: "%s".',
                self::TASK_TYPE,
                $task->taskType,
                $task->id
            ));
        }

        $email = $task->arguments['email'] ?? null;
        $token = $task->arguments['token'] ?? null;
        $expiresAt = $task->arguments['expiresAt'] ?? null;

        $invalidArguments = [];

        if (!is_string($email) || $email === '') {
            $invalidArguments[] = 'email';
        }

        if (!is_string($token) || $token === '') {
            $invalidArguments[] = 'token';
        }

        if ($invalidArguments !== []) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid task arguments for sign-up challenge email. Task id: "%s", invalid: %s',
                $task->id,
                implode(', ', $invalidArguments)
            ));
        }

        if (!is_string($expiresAt) || $expiresAt === '') {
            throw new \InvalidArgumentException(sprintf(
                'Missing or invalid "expiresAt" in task arguments for sign-up email. Task id: "%s"',
                $task->id
            ));
        }

        return new self(
            id: $task->id,
            taskType: $task->taskType,
            arguments: $task->arguments,
            processed: $task->processed,
            processedAt: $task->processedAt,
        );
    }

    public function getEmail(): string
    {
        /** @var string $email */
        $email = $this->arguments['email'];

        return $email;
    }

    public function getToken(): string
    {
        /** @var string $token */
        $token = $this->arguments['token'];

        return $token;
    }

    public function getExpiresAt(): string
    {
        /** @var string $expiresAt */
        $expiresAt = $this->arguments['expiresAt'];

        return $expiresAt;
    }
}
