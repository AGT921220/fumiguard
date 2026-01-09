<?php

namespace App\Application\Tenancy;

final class TenantContext
{
    private ?int $tenantId = null;

    public function setTenantId(int $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function tenantId(): ?int
    {
        return $this->tenantId;
    }

    public function requireTenantId(): int
    {
        if ($this->tenantId === null) {
            throw new \RuntimeException('Tenant context is not set.');
        }

        return $this->tenantId;
    }
}

