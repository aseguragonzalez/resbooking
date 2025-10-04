<?php

declare(strict_types=1);

namespace App\Application\Projects\AddUser;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Projects\ValueObjects\User;
use App\Domain\Shared\Email;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<AddUserRequest>
 * @extends UseCase<AddUserRequest>
 */
final class AddUser extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param AddUserRequest $request
     */
    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $email = new Email($request->username);
        $project->addUser(new User($email));
        $this->projectRepository->save($project);
    }
}
