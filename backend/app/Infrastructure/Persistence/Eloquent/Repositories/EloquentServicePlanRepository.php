<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\ServicePlanRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ServicePlan;

final class EloquentServicePlanRepository extends TenantScopedRepository implements ServicePlanRepository
{
    public function list(): array
    {
        return ServicePlan::query()
            ->where('tenant_id', $this->tenantId())
            ->orderBy('name')
            ->get()
            ->map(fn (ServicePlan $p) => $this->toArray($p))
            ->all();
    }

    public function get(int $id): ?array
    {
        $p = ServicePlan::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        return $p ? $this->toArray($p) : null;
    }

    public function create(array $data): array
    {
        $p = ServicePlan::query()->create([
            'tenant_id' => $this->tenantId(),
            'name' => $data['name'],
            'checklist_template' => $data['checklist_template'] ?? null,
            'certificate_template' => $data['certificate_template'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $this->toArray($p);
    }

    public function update(int $id, array $data): ?array
    {
        $p = ServicePlan::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        if (! $p) {
            return null;
        }

        $p->fill($data);
        $p->save();

        return $this->toArray($p);
    }

    public function delete(int $id): bool
    {
        return (bool) ServicePlan::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->delete();
    }

    private function toArray(ServicePlan $p): array
    {
        return [
            'id' => (int) $p->id,
            'name' => (string) $p->name,
            'is_active' => (bool) $p->is_active,
            'checklist_template' => $p->checklist_template,
            'certificate_template' => $p->certificate_template,
        ];
    }
}

