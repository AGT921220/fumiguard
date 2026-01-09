<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\UserRepository;
use App\Domain\Entities\User as DomainUser;
use App\Domain\Enums\UserRole;
use App\Infrastructure\Persistence\Eloquent\Models\User;

final class EloquentUserRepository implements UserRepository
{
    public function findByEmail(string $email): ?DomainUser
    {
        $model = User::query()->where('email', $email)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findById(int $id): ?DomainUser
    {
        $model = User::query()->whereKey($id)->first();

        return $model ? $this->toDomain($model) : null;
    }

    private function toDomain(User $model): DomainUser
    {
        return new DomainUser(
            id: (int) $model->id,
            tenantId: (int) $model->tenant_id,
            name: (string) $model->name,
            email: (string) $model->email,
            role: UserRole::from((string) $model->role),
            passwordHash: (string) $model->password,
        );
    }
}

