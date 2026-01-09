<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\Customer;
use App\Infrastructure\Persistence\Eloquent\Models\Site;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'customer_id' => Customer::factory(),
            'name' => fake()->company().' - '.fake()->word(),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->countryCode(),
            'lat' => fake()->optional()->latitude(),
            'lng' => fake()->optional()->longitude(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn () => [
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->id,
        ]);
    }
}

