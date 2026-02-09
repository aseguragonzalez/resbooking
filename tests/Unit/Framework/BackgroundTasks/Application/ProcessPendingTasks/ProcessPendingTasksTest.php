<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\BackgroundTasks\Application\ProcessPendingTasks;

use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasks;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksCommand;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksHandler;
use Framework\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ProcessPendingTasksTest extends TestCase
{
    private TaskRepository&MockObject $taskRepository;
    private TaskBus&MockObject $taskBus;
    private LoggerInterface $logger;
    private ProcessPendingTasks $service;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->taskBus = $this->createMock(TaskBus::class);
        $this->logger = $this->createStub(LoggerInterface::class);
        $this->service = new ProcessPendingTasksHandler(
            $this->taskRepository,
            $this->taskBus,
            $this->logger,
        );
    }

    public function testExecuteFetchesPendingTasksAndDispatchesEachThenMarksProcessed(): void
    {
        $task = Task::build('id-1', 'send_email', ['to' => 'a@b.com']);

        $this->taskRepository->expects($this->once())
            ->method('findPending')
            ->with(50)
            ->willReturn([$task]);

        $this->taskBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (Task $t): bool {
                return $t->taskType === 'send_email' && $t->arguments === ['to' => 'a@b.com'];
            }));

        $this->taskRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Task $saved): bool {
                return $saved->id === 'id-1'
                    && $saved->processed === true
                    && $saved->processedAt instanceof \DateTimeImmutable;
            }));

        $this->service->execute(new ProcessPendingTasksCommand(limit: 50));
    }

    public function testExecuteProcessesMultiplePendingTasks(): void
    {
        $task1 = Task::build('id-1', 'type_a', []);
        $task2 = Task::build('id-2', 'type_b', ['x' => 1]);

        $this->taskRepository->expects($this->once())
            ->method('findPending')
            ->with(10)
            ->willReturn([$task1, $task2]);

        $this->taskBus->expects($this->exactly(2))->method('dispatch');

        $recordedIds = [];
        $this->taskRepository->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(function (Task $subject) use (&$recordedIds): void {
                if ($subject->processed) {
                    $recordedIds[] = $subject->id;
                }
            });

        $this->service->execute(new ProcessPendingTasksCommand(limit: 10));

        $this->assertSame(['id-1', 'id-2'], $recordedIds);
    }

    public function testExecuteDoesNothingWhenNoPendingTasks(): void
    {
        $this->taskRepository->expects($this->once())
            ->method('findPending')
            ->with(100)
            ->willReturn([]);

        $this->taskBus->expects($this->never())->method('dispatch');
        $this->taskRepository->expects($this->never())->method('save');

        $this->service->execute(new ProcessPendingTasksCommand(limit: 100));
    }

    public function testExecuteLogsErrorAndDoesNotMarkProcessedWhenDispatchThrows(): void
    {
        $task = Task::build('id-1', 'send_email', []);
        $this->taskRepository
            ->expects($this->once())
            ->method('findPending')
            ->willReturn([$task]);
        $this->taskBus
            ->expects($this->once())
            ->method('dispatch')
            ->willThrowException(new \RuntimeException('Handler failed'));

        $this->taskRepository->expects($this->once())->method('save')->with(
            $this->callback(function (Task $saved): bool {
                return $saved->id === 'id-1'
                    && $saved->processed === true
                    && $saved->processedAt instanceof \DateTimeImmutable;
            }),
        );

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                'Failed to process task',
                $this->callback(function (array $context): bool {
                    return isset($context['taskId'], $context['taskType'], $context['error'])
                        && $context['taskId'] === 'id-1'
                        && $context['taskType'] === 'send_email'
                        && $context['error'] === 'Handler failed';
                }),
            );

        $service = new ProcessPendingTasksHandler(
            $this->taskRepository,
            $this->taskBus,
            $logger,
        );
        $service->execute(new ProcessPendingTasksCommand(limit: 10));
    }
}
