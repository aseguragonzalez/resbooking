<?php

declare(strict_types=1);

namespace App\Application\Users\ResetUserCredential;

use App\Domain\Users\UserRepository;
use App\Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<ResetUserCredentialRequest>
 * @extends UseCase<ResetUserCredentialRequest>
 */
final class ResetUserCredential extends UseCase
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param ResetUserCredentialRequest $request
     */
    public function execute($request): void
    {
        $user = $this->userRepository->getById($request->username);
        $user->resetCredential();
        $this->userRepository->save($user);
    }
}
