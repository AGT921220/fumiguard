<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\ServiceReport;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use App\Infrastructure\Persistence\Eloquent\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\ServiceReport>
 */
class ServiceReportFactory extends Factory
{
    protected $model = ServiceReport::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'work_order_id' => WorkOrder::factory(),
            'status' => 'DRAFT',
            'locked' => false,
            'started_at' => now(),
            'finalized_at' => null,
            'checklist' => null,
            'notes' => null,
        ];
    }
}

