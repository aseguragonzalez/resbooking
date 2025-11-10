<?php

declare(strict_types=1);

namespace Application\Projects\AddOpenCloseEvent;

use Domain\Projects\ProjectRepository;
use Domain\Shared\Turn;
use Domain\Shared\ValueObjects\OpenCloseEvent;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<AddOpencloseEventCommand>
 */
final class AddOpenCloseEvent extends ApplicationService
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param AddOpencloseEventCommand $command
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
