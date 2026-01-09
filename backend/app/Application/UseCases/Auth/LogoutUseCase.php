<?php

namespace App\Application\UseCases\Auth;

use App\Application\Ports\AuthTokenService;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final readonly class LogoutUseCase
{
    public function __construct(
        private AuthTokenService $tokens,
    ) {
    }

    public function execute(?int $tokenId): void
    {
        if ($tokenId === null) {
            throw new UnauthorizedHttpException('', 'Token inválido.');
        }

        $this->tokens->revokeToken($tokenId);
    }
}

