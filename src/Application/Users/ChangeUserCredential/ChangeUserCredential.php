<?php

declare(strict_types=1);

namespace App\Application\Users\ChangeUserCredential;

use App\Domain\Shared\Password;
use App\Domain\Users\UserRepository;
use App\Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<ChangeUserCredentialRequest>
 * @extends UseCase<ChangeUserCredentialRequest>
 */
final class ChangeUserCredential extends UseCase
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param ChangeUserCredentialRequest $request
     */
    public function execute($request): void
    {
        $user = $this->userRepository->getById($request->username);
        $user->changeCredential(new Password($request->password));
        $this->userRepository->save($user);
    }
}
