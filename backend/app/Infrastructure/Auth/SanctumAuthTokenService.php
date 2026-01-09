<?php

namespace App\Infrastructure\Auth;

use App\Application\Ports\AuthTokenService;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

final class SanctumAuthTokenService implements AuthTokenService
{
    public function createTokenForUser(int $userId, string $tokenName = 'api'): array
    {
        /** @var User $user */
        $user = User::query()->whereKey($userId)->firstOrFail();

        $new = $user->createToken($tokenName);

        /** @var PersonalAccessToken|null $tokenModel */
        $tokenModel = $new->accessToken;

        return [
            'token' => $new->plainTextToken,
            'token_id' => $tokenModel?->id ?? 0,
        ];
    }

    public function revokeToken(int $tokenId): void
    {
        PersonalAccessToken::query()->whereKey($tokenId)->delete();
    }
}

