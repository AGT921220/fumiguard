<?php

namespace App\Application\Auth;

use App\Domain\Enums\UserRole;

final class UserContext
{
    private ?int $userId = null;
    private ?UserRole $role = null;

    public function set(int $userId, UserRole $role): void
    {
        $this->userId = $userId;
        $this->role = $role;
    }

    public function userId(): ?int
    {
        return $this->userId;
    }

    public function role(): ?UserRole
    {
        return $this->role;
    }

    public function requireUserId(): int
    {
        if ($this->userId === null) {
            throw new \RuntimeException('User context is not set.');
        }

        return $this->userId;
    }

    public function requireRole(): UserRole
    {
        if ($this->role === null) {
            throw new \RuntimeException('User context is not set.');
        }

        return $this->role;
    }
}

