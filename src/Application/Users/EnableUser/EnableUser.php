<?php

declare(strict_types=1);

namespace App\Application\Users\EnableUser;

use App\Domain\Users\UserRepository;
use Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<EnableUserRequest>
 * @extends UseCase<EnableUserRequest>
 */
final class EnableUser extends UseCase
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param EnableUserRequest $request
     */
    public function execute($request): void
    {
        $user = $this->userRepository->getById($request->username);
        $user->enable();
        $this->userRepository->save($user);
    }
}
