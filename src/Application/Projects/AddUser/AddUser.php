<?php

declare(strict_types=1);

namespace App\Application\Projects\AddUser;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Projects\ValueObjects\User;
use App\Domain\Shared\Email;
use App\Domain\Users\{UserFactory, UserRepository};
use App\Seedwork\Application\UseCase;

/**
 * @extends UseCase<AddUserRequest>
 */
final class AddUser extends UseCase
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly UserFactory $userFactory,
        private readonly UserRepository $userRepository
    ) {
    }

    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $email = new Email($request->username);
        $user = $request->isAdmin
            ? $this->userFactory->createNewAdmin($email)
            : $this->userFactory->createNewUser($email);
        $project->addUser(new User($email));
        $this->userRepository->save($user);
        $this->projectRepository->save($project);
    }
}
