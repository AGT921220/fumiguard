<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\ChemicalUsage;
use App\Infrastructure\Persistence\Eloquent\Models\ServiceReport;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\ChemicalUsage>
 */
class ChemicalUsageFactory extends Factory
{
    protected $model = ChemicalUsage::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'service_report_id' => ServiceReport::factory(),
            'chemical_name' => fake()->word().' '.fake()->word(),
            'quantity' => fake()->randomFloat(3, 0.1, 10.0),
            'unit' => fake()->randomElement(['ml', 'l', 'g', 'kg']),
        ];
    }
}

