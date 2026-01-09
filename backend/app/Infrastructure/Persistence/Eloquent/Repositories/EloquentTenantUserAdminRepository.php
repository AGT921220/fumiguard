<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\TenantUserAdminRepository;
use App\Infrastructure\Persistence\Eloquent\Models\User;

final class EloquentTenantUserAdminRepository implements TenantUserAdminRepository
{
    public function countTechnicians(int $tenantId): int
    {
        return (int) User::query()
            ->where('tenant_id', $tenantId)
            ->where('role', 'TECHNICIAN')
            ->count();
    }

    public function createUser(int $tenantId, array $data): array
    {
        $u = User::query()->create([
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        return [
            'id' => (int) $u->id,
            'tenant_id' => (int) $u->tenant_id,
            'name' => (string) $u->name,
            'email' => (string) $u->email,
            'role' => (string) $u->role,
        ];
    }
}

