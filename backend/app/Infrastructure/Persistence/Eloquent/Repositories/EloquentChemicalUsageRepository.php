<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\ChemicalUsageRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ChemicalUsage;

final class EloquentChemicalUsageRepository extends TenantScopedRepository implements ChemicalUsageRepository
{
    public function create(array $data): array
    {
        $c = ChemicalUsage::query()->create([
            'tenant_id' => $this->tenantId(),
            'service_report_id' => $data['service_report_id'],
            'chemical_name' => $data['chemical_name'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
        ]);

        return $this->toArray($c);
    }

    public function listByReport(int $serviceReportId): array
    {
        return ChemicalUsage::query()
            ->where('tenant_id', $this->tenantId())
            ->where('service_report_id', $serviceReportId)
            ->orderByDesc('id')
            ->get(['id', 'service_report_id', 'chemical_name', 'quantity', 'unit'])
            ->map(fn (ChemicalUsage $c) => $this->toArray($c))
            ->all();
    }

    private function toArray(ChemicalUsage $c): array
    {
        return [
            'id' => (int) $c->id,
            'service_report_id' => (int) $c->service_report_id,
            'chemical_name' => (string) $c->chemical_name,
            'quantity' => (string) $c->quantity,
            'unit' => (string) $c->unit,
        ];
    }
}

