<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\AppointmentRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
use Carbon\CarbonImmutable;

final class EloquentAppointmentRepository extends TenantScopedRepository implements AppointmentRepository
{
    public function create(array $data): array
    {
        $scheduledAt = CarbonImmutable::parse($data['scheduled_at'])->toDateTimeString();

        $a = Appointment::query()->create([
            'tenant_id' => $this->tenantId(),
            'customer_id' => $data['customer_id'],
            'site_id' => $data['site_id'],
            'service_plan_id' => $data['service_plan_id'] ?? null,
            'recurrence_rule_id' => $data['recurrence_rule_id'] ?? null,
            'scheduled_at' => $scheduledAt,
            'status' => $data['status'] ?? 'SCHEDULED',
            'notes' => $data['notes'] ?? null,
        ]);

        return $this->toArray($a);
    }

    public function get(int $id): ?array
    {
        $a = Appointment::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        return $a ? $this->toArray($a) : null;
    }

    public function listBetween(string $startIso, string $endIso): array
    {
        return Appointment::query()
            ->where('tenant_id', $this->tenantId())
            ->whereBetween('scheduled_at', [$startIso, $endIso])
            ->orderBy('scheduled_at')
            ->get(['id', 'customer_id', 'site_id', 'scheduled_at', 'status'])
            ->map(fn (Appointment $a) => [
                'id' => (int) $a->id,
                'customer_id' => (int) $a->customer_id,
                'site_id' => (int) $a->site_id,
                'scheduled_at' => $a->scheduled_at->toISOString(),
                'status' => (string) $a->status,
            ])
            ->all();
    }

    private function toArray(Appointment $a): array
    {
        return [
            'id' => (int) $a->id,
            'customer_id' => (int) $a->customer_id,
            'site_id' => (int) $a->site_id,
            'service_plan_id' => $a->service_plan_id !== null ? (int) $a->service_plan_id : null,
            'recurrence_rule_id' => $a->recurrence_rule_id !== null ? (int) $a->recurrence_rule_id : null,
            'scheduled_at' => $a->scheduled_at->toISOString(),
            'status' => (string) $a->status,
            'notes' => $a->notes !== null ? (string) $a->notes : null,
        ];
    }
}

