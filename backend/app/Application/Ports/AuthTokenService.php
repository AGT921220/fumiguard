<?php

namespace App\Application\Ports;

interface AuthTokenService
{
    /**
     * @return array{token: string, token_id: int}
     */
    public function createTokenForUser(int $userId, string $tokenName = 'api'): array;

    public function revokeToken(int $tokenId): void;
}

