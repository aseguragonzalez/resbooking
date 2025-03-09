<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveUser;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Projects\ValueObjects\User;
use App\Domain\Shared\Email;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<RemoveUserRequest>
 * @extends UseCase<RemoveUserRequest>
 */
final class RemoveUser extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param RemoveUserRequest $request
     */
    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $project->removeUser(new User(new Email($request->username)));
        $this->projectRepository->save($project);
    }
}
