<?php

declare(strict_types=1);

namespace Application\Projects\AddOpenCloseEvent;

use Domain\Projects\ProjectRepository;
use Domain\Shared\Turn;
use Domain\Projects\ValueObjects\OpenCloseEvent;

final readonly class AddOpenCloseEventService implements AddOpenCloseEvent
{
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function execute(AddOpenCloseEventCommand $command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        $turn = Turn::getByStartTime($command->startTime);
        $openCloseEvent = new OpenCloseEvent($command->date, $command->isAvailable, $turn);
        $project->addOpenCloseEvent($openCloseEvent);
        $this->projectRepository->save($project);
    }
}
