<?php

declare(strict_types=1);

namespace App\Application\Projects\AddOpenCloseEvent;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\Turn;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use App\Seedwork\Application\UseCase;

/**
 * @extends UseCase<AddOpenCloseEventRequest>
 */
final class AddOpenCloseEvent extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param AddOpenCloseEventRequest $request
     */
    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $turn = Turn::getByStartTime($request->startTime);
        $openCloseEvent = new OpenCloseEvent($request->date, $request->isAvailable, $turn);
        $project->addOpenCloseEvent($openCloseEvent);
        $this->projectRepository->save($project);
    }
}
