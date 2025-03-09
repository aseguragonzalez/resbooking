<?php

declare(strict_types=1);

namespace App\Application\Users\UnlockUser;

use App\Domain\Users\UserRepository;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<UnlockUserRequest>
 * @extends UseCase<UnlockUserRequest>
 */
final class UnlockUser extends UseCase
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param UnlockUserRequest $request
     */
    public function execute($request): void
    {
        $user = $this->userRepository->getById($request->username);
        $user->unlock();
        $this->userRepository->save($user);
    }
}
