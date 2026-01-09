<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
use App\Infrastructure\Persistence\Eloquent\Models\Customer;
use App\Infrastructure\Persistence\Eloquent\Models\RecurrenceRule;
use App\Infrastructure\Persistence\Eloquent\Models\ServicePlan;
use App\Infrastructure\Persistence\Eloquent\Models\Site;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'customer_id' => Customer::factory(),
            'site_id' => Site::factory(),
            'service_plan_id' => ServicePlan::factory(),
            'recurrence_rule_id' => RecurrenceRule::factory(),
            'scheduled_at' => fake()->dateTimeBetween('-3 days', '+7 days'),
            'status' => 'SCHEDULED',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => ['tenant_id' => $tenant->id]);
    }
}

