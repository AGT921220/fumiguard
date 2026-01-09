<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\EvidenceRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Evidence;

final class EloquentEvidenceRepository extends TenantScopedRepository implements EvidenceRepository
{
    public function create(array $data): array
    {
        $e = Evidence::query()->create([
            'tenant_id' => $this->tenantId(),
            'service_report_id' => $data['service_report_id'],
            'path' => $data['path'],
            'mime' => $data['mime'] ?? null,
        ]);

        return $this->toArray($e);
    }

    public function listByReport(int $serviceReportId): array
    {
        return Evidence::query()
            ->where('tenant_id', $this->tenantId())
            ->where('service_report_id', $serviceReportId)
            ->orderByDesc('id')
            ->get(['id', 'service_report_id', 'path', 'mime'])
            ->map(fn (Evidence $e) => $this->toArray($e))
            ->all();
    }

    private function toArray(Evidence $e): array
    {
        return [
            'id' => (int) $e->id,
            'service_report_id' => (int) $e->service_report_id,
            'path' => (string) $e->path,
            'mime' => $e->mime !== null ? (string) $e->mime : null,
        ];
    }
}

