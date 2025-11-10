<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects;

use Domain\Projects\Entities\Project;
use Domain\Projects\ProjectRepository;
use Infrastructure\Adapters\Repositories\Projects\Models\Project as ProjectModel;

final class InFileProjectRepository implements ProjectRepository
{
    /**
     * @var array<string, Project>
     */
    private array $projects = [];

    public function __construct(
        private readonly string $filePath = __DIR__ . '/inmemory_projects.json',
        private readonly ProjectsMapper $mapper = new ProjectsMapper(),
    ) {
        $file = $this->filePath;
        if (!file_exists($file)) {
            return;
        }

        $fileContent = file_get_contents($file);
        if ($fileContent === false) {
            return;
        }

        $data = json_decode($fileContent, true);
        if (!is_array($data)) {
            return;
        }

        foreach ($data as $projectData) {
            $json = json_decode(json_encode($projectData), false, 512, JSON_THROW_ON_ERROR);
            $projectModel = ProjectModel::fromArray((array) $json);
            $project = $this->mapper->mapToDomain($projectModel);
            $this->projects[$project->getId()] = $project;
        }
    }

    public function __destruct()
    {
        $models = array_map(fn (Project $project) => $this->mapper->mapToModel($project), $this->projects);
        file_put_contents($this->filePath, json_encode($models, JSON_PRETTY_PRINT));
    }

    /**
     * @param Project $aggregateRoot
     */
    public function save($aggregateRoot): void
    {
        $this->projects[$aggregateRoot->getId()] = $aggregateRoot;
    }

    public function getById(string $id): Project
    {
        return $this->projects[$id];
    }

    public function exist(string $id): bool
    {
        return isset($this->projects[$id]);
    }
}
