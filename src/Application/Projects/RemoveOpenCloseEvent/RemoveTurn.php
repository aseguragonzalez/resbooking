<?php

declare(strict_types=1);

namespace Application\Projects\RemoveOpenCloseEvent;

use Domain\Projects\ProjectRepository;
use Domain\Shared\ValueObjects\OpenCloseEvent;
use Seedwork\Application\ApplicationService;

/**
 * @template-extends ApplicationService<RemoveOpenCloseEventCommand>
 * @extends ApplicationService<RemoveOpenCloseEventCommand>
 */
final class RemoveOpenCloseEvent extends ApplicationService
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param RemoveOpenCloseEventCommand $command
     */
    public function execute($command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        $project->removeOpenCloseEvents(
            fn (OpenCloseEvent $openCloseEvent) =>
                $openCloseEvent->date->format('Y-m-d') === $command->date->format('Y-m-d')
                    && $openCloseEvent->turn === $command->turn,
        );
        $this->projectRepository->save($project);
    }
}
