<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\BackgroundTasks\Tasks;

use Framework\BackgroundTasks\Domain\Task;
use Infrastructure\Ports\BackgroundTasks\Tasks\ResetPasswordChallengeEmailTask;
use PHPUnit\Framework\TestCase;

final class ResetPasswordChallengeEmailTaskTest extends TestCase
{
    private function validTask(): Task
    {
        return Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => 'user@example.com',
                'token' => 'token-123',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );
    }

    public function testFromTaskWithValidArgumentsReturnsInstanceAndGettersReturnCorrectValues(): void
    {
        $task = $this->validTask();

        $resetTask = ResetPasswordChallengeEmailTask::fromTask($task);

        $this->assertSame('task-1', $resetTask->id);
        $this->assertSame('send_reset_password_challenge_email', $resetTask->taskType);
        $this->assertSame('user@example.com', $resetTask->getEmail());
        $this->assertSame('token-123', $resetTask->getToken());
        $this->assertSame('2024-01-02T15:30:00+00:00', $resetTask->getExpiresAt());
    }

    public function testFromTaskThrowsWhenTaskTypeIsWrong(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'other_task_type',
            arguments: [
                'email' => 'user@example.com',
                'token' => 'token-123',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected task type "send_reset_password_challenge_email", got "other_task_type". Task id: "task-1".'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskThrowsWhenEmailIsMissing(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'token' => 'token-123',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for reset-password challenge email. Task id: "task-1", invalid: email'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskThrowsWhenEmailIsEmpty(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => '',
                'token' => 'token-123',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for reset-password challenge email. Task id: "task-1", invalid: email'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskThrowsWhenEmailIsNotString(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => 123,
                'token' => 'token-123',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for reset-password challenge email. Task id: "task-1", invalid: email'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskThrowsWhenTokenIsMissing(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => 'user@example.com',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for reset-password challenge email. Task id: "task-1", invalid: token'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskThrowsWhenTokenIsEmpty(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => 'user@example.com',
                'token' => '',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for reset-password challenge email. Task id: "task-1", invalid: token'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskThrowsWhenExpiresAtIsMissing(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => 'user@example.com',
                'token' => 'token-123',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing or invalid "expiresAt" in task arguments for reset-password email. Task id: "task-1"'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskThrowsWhenExpiresAtIsEmpty(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => 'user@example.com',
                'token' => 'token-123',
                'expiresAt' => '',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing or invalid "expiresAt" in task arguments for reset-password email. Task id: "task-1"'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }

    public function testFromTaskReportsMultipleInvalidArgumentsInOneException(): void
    {
        $task = Task::build(
            id: 'task-1',
            taskType: 'send_reset_password_challenge_email',
            arguments: [
                'email' => '',
                'token' => '',
                'expiresAt' => '2024-01-02T15:30:00+00:00',
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid task arguments for reset-password challenge email. Task id: "task-1", invalid: email, token'
        );

        ResetPasswordChallengeEmailTask::fromTask($task);
    }
}
