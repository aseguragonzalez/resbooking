<?php

declare(strict_types=1);

namespace App\Application\Users\LockUser;

use App\Domain\Users\UserRepository;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<LockUserRequest>
 * @extends UseCase<LockUserRequest>
 */
final class LockUser extends UseCase
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param LockUserRequest $request
     */
    public function execute($request): void
    {
        $user = $this->userRepository->getById($request->username);
        $user->lock();
        $this->userRepository->save($user);
    }
}
