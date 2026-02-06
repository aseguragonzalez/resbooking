<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Infrastructure;

use Framework\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\BackgroundTasks\Domain\Task;
use PDO;

final readonly class SqlTaskRepository implements TaskRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function save(Task $task): void
    {
        $id = uniqid('', true);
        $argumentsJson = json_encode($task->arguments, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT);
        $createdAt = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $sql = <<<SQL
            INSERT INTO background_tasks (id, task_type, arguments, created_at, processed, processed_at)
            VALUES (:id, :task_type, :arguments, :created_at, :processed, :processed_at)
            SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'task_type' => $task->taskType,
            'arguments' => $argumentsJson,
            'created_at' => $createdAt,
            'processed' => 0,
            'processed_at' => null,
        ]);
    }
}
