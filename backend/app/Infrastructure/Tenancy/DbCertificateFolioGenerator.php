<?php

namespace App\Infrastructure\Tenancy;

use App\Application\Ports\CertificateFolioGenerator;
use App\Application\Tenancy\TenantContext;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Support\Facades\DB;

final readonly class DbCertificateFolioGenerator implements CertificateFolioGenerator
{
    public function __construct(private TenantContext $tenantContext)
    {
    }

    public function nextFolio(): string
    {
        $tenantId = $this->tenantContext->requireTenantId();

        return (string) DB::transaction(function () use ($tenantId) {
            /** @var Tenant $tenant */
            $tenant = Tenant::query()->whereKey($tenantId)->lockForUpdate()->firstOrFail();

            $tenant->certificate_sequence = (int) $tenant->certificate_sequence + 1;
            $tenant->save();

            $seq = (int) $tenant->certificate_sequence;

            return strtoupper($tenant->slug).'-'.str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
        });
    }
}

