<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\ServiceReportRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ServiceReport;

final class EloquentServiceReportRepository extends TenantScopedRepository implements ServiceReportRepository
{
    public function createForWorkOrder(int $workOrderId): array
    {
        $r = ServiceReport::query()->create([
            'tenant_id' => $this->tenantId(),
            'work_order_id' => $workOrderId,
            'status' => 'DRAFT',
            'locked' => false,
            'started_at' => now(),
            'finalized_at' => null,
            'checklist' => null,
            'notes' => null,
        ]);

        return $this->toArray($r);
    }

    public function getByWorkOrderId(int $workOrderId): ?array
    {
        $r = ServiceReport::query()
            ->where('tenant_id', $this->tenantId())
            ->where('work_order_id', $workOrderId)
            ->first();

        return $r ? $this->toArray($r) : null;
    }

    public function update(int $id, array $data): ?array
    {
        $r = ServiceReport::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        if (! $r) {
            return null;
        }

        $r->fill($data);
        $r->save();

        return $this->toArray($r);
    }

    private function toArray(ServiceReport $r): array
    {
        return [
            'id' => (int) $r->id,
            'work_order_id' => (int) $r->work_order_id,
            'status' => (string) $r->status,
            'locked' => (bool) $r->locked,
            'started_at' => $r->started_at->toISOString(),
            'finalized_at' => $r->finalized_at?->toISOString(),
            'checklist' => $r->checklist,
            'notes' => $r->notes !== null ? (string) $r->notes : null,
        ];
    }
}

