<?php

declare(strict_types=1);

namespace App\Application\Users\DisableUser;

use App\Domain\Users\UserRepository;
use App\Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<DisableUserRequest>
 * @extends UseCase<DisableUserRequest>
 */
final class DisableUser extends UseCase
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param DisableUserRequest $request
     */
    public function execute($request): void
    {
        $user = $this->userRepository->getById($request->username);
        $user->disable();
        $this->userRepository->save($user);
    }
}
