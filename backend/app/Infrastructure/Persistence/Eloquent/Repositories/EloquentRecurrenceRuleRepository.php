<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\RecurrenceRuleRepository;
use App\Infrastructure\Persistence\Eloquent\Models\RecurrenceRule;

final class EloquentRecurrenceRuleRepository extends TenantScopedRepository implements RecurrenceRuleRepository
{
    public function create(array $data): array
    {
        $r = RecurrenceRule::query()->create([
            'tenant_id' => $this->tenantId(),
            'frequency' => $data['frequency'],
            'day_of_month' => $data['day_of_month'],
            'interval_months' => $data['interval_months'],
            'starts_on' => $data['starts_on'] ?? null,
        ]);

        return $this->toArray($r);
    }

    public function get(int $id): ?array
    {
        $r = RecurrenceRule::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        return $r ? $this->toArray($r) : null;
    }

    private function toArray(RecurrenceRule $r): array
    {
        return [
            'id' => (int) $r->id,
            'frequency' => (string) $r->frequency,
            'day_of_month' => (int) $r->day_of_month,
            'interval_months' => (int) $r->interval_months,
            'starts_on' => $r->starts_on?->toDateString(),
        ];
    }
}

