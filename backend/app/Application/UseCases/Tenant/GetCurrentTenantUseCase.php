<?php

namespace App\Application\UseCases\Tenant;

use App\Application\Ports\TenantRepository;
use App\Application\Tenancy\TenantContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GetCurrentTenantUseCase
{
    public function __construct(
        private TenantRepository $tenants,
        private TenantContext $tenantContext,
    ) {
    }

    /**
     * @return array{id:int,name:string,slug:string}
     */
    public function execute(): array
    {
        $tenantId = $this->tenantContext->requireTenantId();

        $tenant = $this->tenants->findById($tenantId);

        if ($tenant === null) {
            throw new NotFoundHttpException('Tenant no encontrado.');
        }

        return [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
        ];
    }
}

