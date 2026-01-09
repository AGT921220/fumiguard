<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\TenantRepository;
use App\Domain\Entities\Tenant as DomainTenant;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;

final class EloquentTenantRepository implements TenantRepository
{
    public function findById(int $id): ?DomainTenant
    {
        $model = Tenant::query()->whereKey($id)->first();

        return $model
            ? new DomainTenant(
                id: (int) $model->id,
                name: (string) $model->name,
                slug: (string) $model->slug,
            )
            : null;
    }
}

