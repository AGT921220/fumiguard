<?php

namespace App\Application\Ports;

interface TenantUserAdminRepository
{
    public function countTechnicians(int $tenantId): int;

    /**
     * @param array{name:string,email:string,password:string,role:string} $data
     * @return array{id:int,tenant_id:int,name:string,email:string,role:string}
     */
    public function createUser(int $tenantId, array $data): array;
}

