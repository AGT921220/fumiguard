<?php

namespace App\Application\UseCases\Auth;

use App\Application\Ports\AuthTokenService;
use App\Application\Ports\UserRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final readonly class LoginUseCase
{
    public function __construct(
        private UserRepository $users,
        private AuthTokenService $tokens,
    ) {
    }

    /**
     * @return array{
     *   token: string,
     *   user: array{id:int,name:string,email:string,tenant_id:int,role:string}
     * }
     */
    public function execute(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || ! Hash::check($password, $user->passwordHash)) {
            throw new UnauthorizedHttpException('', 'Credenciales inválidas.');
        }

        $created = $this->tokens->createTokenForUser($user->id, 'api');

        return [
            'token' => $created['token'],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenantId,
                'role' => $user->role->value,
            ],
        ];
    }
}

