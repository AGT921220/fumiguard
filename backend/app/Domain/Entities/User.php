<?php

namespace App\Domain\Entities;

use App\Domain\Enums\UserRole;

final readonly class User
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public string $name,
        public string $email,
        public UserRole $role,
        public string $passwordHash,
    ) {
    }
}

