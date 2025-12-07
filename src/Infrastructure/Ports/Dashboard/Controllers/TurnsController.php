<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Projects\UpdateTurns\UpdateTurns;
use Application\Projects\UpdateTurns\UpdateTurnsCommand;
use Domain\Projects\Repositories\ProjectRepository;
use Infrastructure\Ports\Dashboard\Models\Turns\Pages\TurnsList;
use Infrastructure\Ports\Dashboard\Models\Turns\Requests\UpdateTurnsRequest;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class TurnsController extends Controller
{
    private const string PROJECT_ID = '69347ea320d5a';

    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly UpdateTurns $updateTurns,
    ) {
    }

    public function turns(): ActionResponse
    {
        $project = $this->projectRepository->getById(self::PROJECT_ID);
        $turnAvailables = $project->getTurns();
        $pageModel = TurnsList::create(turnAvailables: $turnAvailables);
        return $this->view(model: $pageModel);
    }

    public function updateTurns(ServerRequestInterface $request): ActionResponse
    {
        $turnRequest = new UpdateTurnsRequest($request);
        $command = new UpdateTurnsCommand(projectId: self::PROJECT_ID, turns: $turnRequest->turns);
        $this->updateTurns->execute($command);
        return $this->redirectToAction(action: 'turns');
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/turns'),
                controller: TurnsController::class,
                action: 'turns',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/turns'),
                controller: TurnsController::class,
                action: 'updateTurns',
                authRequired: true
            ),
        ];
    }
}
