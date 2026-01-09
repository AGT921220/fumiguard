<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\RecurrenceRule;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\RecurrenceRule>
 */
class RecurrenceRuleFactory extends Factory
{
    protected $model = RecurrenceRule::class;

    public function definition(): array
    {
        $frequency = fake()->randomElement(['MONTHLY', 'QUARTERLY']);

        return [
            'tenant_id' => Tenant::factory(),
            'frequency' => $frequency,
            'day_of_month' => fake()->numberBetween(1, 28),
            'interval_months' => $frequency === 'QUARTERLY' ? 3 : 1,
            'starts_on' => fake()->optional()->date(),
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => ['tenant_id' => $tenant->id]);
    }
}

