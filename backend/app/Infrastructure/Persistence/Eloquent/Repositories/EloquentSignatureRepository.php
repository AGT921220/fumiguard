<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\SignatureRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Signature;

final class EloquentSignatureRepository extends TenantScopedRepository implements SignatureRepository
{
    public function create(array $data): array
    {
        $s = Signature::query()->create([
            'tenant_id' => $this->tenantId(),
            'service_report_id' => $data['service_report_id'],
            'signed_by_name' => $data['signed_by_name'],
            'signed_by_role' => $data['signed_by_role'] ?? null,
            'signature_data' => $data['signature_data'],
            'signed_at' => $data['signed_at'],
        ]);

        return $this->toArray($s);
    }

    public function listByReport(int $serviceReportId): array
    {
        return Signature::query()
            ->where('tenant_id', $this->tenantId())
            ->where('service_report_id', $serviceReportId)
            ->orderByDesc('id')
            ->get(['id', 'service_report_id', 'signed_by_name', 'signed_by_role', 'signed_at'])
            ->map(fn (Signature $s) => $this->toArray($s))
            ->all();
    }

    private function toArray(Signature $s): array
    {
        return [
            'id' => (int) $s->id,
            'service_report_id' => (int) $s->service_report_id,
            'signed_by_name' => (string) $s->signed_by_name,
            'signed_by_role' => $s->signed_by_role !== null ? (string) $s->signed_by_role : null,
            'signed_at' => $s->signed_at->toISOString(),
        ];
    }
}

