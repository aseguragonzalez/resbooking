<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveTurn;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\ValueObjects\TurnAvailability;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<RemoveTurnRequest>
 * @extends UseCase<RemoveTurnRequest>
 */
final class RemoveTurn extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param RemoveTurnRequest $request
     */
    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $project->removeTurns(
            fn (TurnAvailability $turn) => $turn->dayOfWeek === $request->dayOfWeek && $turn->turn === $request->turn
        );
        $this->projectRepository->save($project);
    }
}
