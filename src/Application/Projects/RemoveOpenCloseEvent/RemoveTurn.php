<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveOpenCloseEvent;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<RemoveOpenCloseEventRequest>
 * @extends UseCase<RemoveOpenCloseEventRequest>
 */
final class RemoveOpenCloseEvent extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param RemoveOpenCloseEventRequest $request
     */
    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $project->removeOpenCloseEvents(
            fn (OpenCloseEvent $openCloseEvent) =>
                $openCloseEvent->date->format('Y-m-d') === $request->date->format('Y-m-d')
                    && $openCloseEvent->turn === $request->turn,
        );
        $this->projectRepository->save($project);
    }
}
