<?php

namespace App\Application\Ports;

use App\Domain\Entities\Tenant;

interface TenantRepository
{
    public function findById(int $id): ?Tenant;
}

