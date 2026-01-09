<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\ServiceReport;
use App\Infrastructure\Persistence\Eloquent\Models\Signature;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\Signature>
 */
class SignatureFactory extends Factory
{
    protected $model = Signature::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'service_report_id' => ServiceReport::factory(),
            'signed_by_name' => fake()->name(),
            'signed_by_role' => fake()->randomElement(['CLIENT', 'TECHNICIAN']),
            'signature_data' => base64_encode(fake()->uuid()),
            'signed_at' => now(),
        ];
    }
}

