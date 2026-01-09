<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\WorkOrderRepository;
use App\Infrastructure\Persistence\Eloquent\Models\WorkOrder;

final class EloquentWorkOrderRepository extends TenantScopedRepository implements WorkOrderRepository
{
    public function createFromAppointment(int $appointmentId): array
    {
        $wo = WorkOrder::query()->create([
            'tenant_id' => $this->tenantId(),
            'appointment_id' => $appointmentId,
            'status' => 'OPEN',
        ]);

        return $this->toArray($wo);
    }

    public function findByAppointmentId(int $appointmentId): ?array
    {
        $wo = WorkOrder::query()
            ->where('tenant_id', $this->tenantId())
            ->where('appointment_id', $appointmentId)
            ->first();

        return $wo ? $this->toArray($wo) : null;
    }

    public function get(int $id): ?array
    {
        $wo = WorkOrder::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        return $wo ? $this->toArray($wo) : null;
    }

    public function update(int $id, array $data): ?array
    {
        $wo = WorkOrder::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        if (! $wo) {
            return null;
        }

        $wo->fill($data);
        $wo->save();

        return $this->toArray($wo);
    }

    private function toArray(WorkOrder $wo): array
    {
        return [
            'id' => (int) $wo->id,
            'appointment_id' => (int) $wo->appointment_id,
            'status' => (string) $wo->status,
            'check_in_at' => $wo->check_in_at?->toISOString(),
            'check_out_at' => $wo->check_out_at?->toISOString(),
            'check_in_lat' => $wo->check_in_lat !== null ? (string) $wo->check_in_lat : null,
            'check_in_lng' => $wo->check_in_lng !== null ? (string) $wo->check_in_lng : null,
            'check_out_lat' => $wo->check_out_lat !== null ? (string) $wo->check_out_lat : null,
            'check_out_lng' => $wo->check_out_lng !== null ? (string) $wo->check_out_lng : null,
        ];
    }
}

