<?php

namespace App\Application\UseCases\Auth;

use App\Application\Ports\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final readonly class GetMeUseCase
{
    public function __construct(
        private UserRepository $users,
    ) {
    }

    /**
     * @return array{id:int,name:string,email:string,tenant_id:int,role:string}
     */
    public function execute(?int $userId): array
    {
        if ($userId === null) {
            throw new UnauthorizedHttpException('', 'No autenticado.');
        }

        $user = $this->users->findById($userId);

        if ($user === null) {
            throw new UnauthorizedHttpException('', 'No autenticado.');
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tenant_id' => $user->tenantId,
            'role' => $user->role->value,
        ];
    }
}

