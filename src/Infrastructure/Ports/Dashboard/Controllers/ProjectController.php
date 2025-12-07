<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Application\Projects\UpdateSettings\UpdateSettings;
use Application\Projects\UpdateSettings\UpdateSettingsCommand;
use Domain\Projects\Repositories\ProjectRepository;
use Infrastructure\Ports\Dashboard\Models\Projects\Pages\UpdateSettings as UpdateSettingsPage;
use Infrastructure\Ports\Dashboard\Models\Projects\Requests\UpdateSettingsRequest;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class ProjectController extends Controller
{
    private const string PROJECT_ID = '69347ea320d5a';

    public function __construct(
        private readonly UpdateSettings $updateSettings,
        private readonly ProjectRepository $projectRepository,
    ) {
    }

    public function settings(): ActionResponse
    {
        $project = $this->projectRepository->getById(self::PROJECT_ID);
        $settings = $project->getSettings();

        $pageModel = UpdateSettingsPage::new(
            email: $settings->email->value,
            hasReminders: $settings->hasReminders,
            name: $settings->name,
            maxNumberOfDiners: $settings->maxNumberOfDiners->value,
            minNumberOfDiners: $settings->minNumberOfDiners->value,
            numberOfTables: $settings->numberOfTables->value,
            phone: $settings->phone->value,
        );
        return $this->view(model: $pageModel);
    }

    public function updateSettings(UpdateSettingsRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            $pageModel = UpdateSettingsPage::withErrors($request, $errors);
            return $this->view("settings", model: $pageModel);
        }

        $this->updateSettings->execute(new UpdateSettingsCommand(
            projectId: self::PROJECT_ID,
            email: $request->email,
            hasReminders: $request->hasRemindersChecked(),
            name: $request->name,
            maxNumberOfDiners: $request->maxNumberOfDiners,
            minNumberOfDiners: $request->minNumberOfDiners,
            numberOfTables: $request->numberOfTables,
            phone: $request->phone,
        ));
        return $this->redirectToAction("settings", ProjectController::class);
    }

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/project/settings'),
                controller: ProjectController::class,
                action: 'settings',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/project/settings'),
                controller: ProjectController::class,
                action: 'updateSettings',
                authRequired: true
            ),
        ];
    }
}
