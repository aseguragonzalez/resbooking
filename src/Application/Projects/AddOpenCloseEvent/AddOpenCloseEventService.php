<?php

declare(strict_types=1);

namespace Application\Projects\AddOpenCloseEvent;

use Domain\Projects\ProjectRepository;
use Domain\Shared\Turn;
use Domain\Shared\ValueObjects\OpenCloseEvent;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<AddOpenCloseEventCommand>
 */
final class AddOpenCloseEventService extends ApplicationService implements AddOpenCloseEvent
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param AddOpenCloseEventCommand $command
     */
    public function execute($command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        $turn = Turn::getByStartTime($command->startTime);
        $openCloseEvent = new OpenCloseEvent($command->date, $command->isAvailable, $turn);
        $project->addOpenCloseEvent($openCloseEvent);
        $this->projectRepository->save($project);
    }
}
