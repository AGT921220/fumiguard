<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use App\Infrastructure\Persistence\Eloquent\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'appointment_id' => Appointment::factory(),
            'status' => 'OPEN',
            'check_in_at' => null,
            'check_in_lat' => null,
            'check_in_lng' => null,
            'check_out_at' => null,
            'check_out_lat' => null,
            'check_out_lng' => null,
        ];
    }
}

