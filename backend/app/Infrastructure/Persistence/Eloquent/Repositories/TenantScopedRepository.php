<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Tenancy\TenantContext;

abstract class TenantScopedRepository
{
    public function __construct(
        protected TenantContext $tenantContext,
    ) {
    }

    protected function tenantId(): int
    {
        return $this->tenantContext->requireTenantId();
    }
}

