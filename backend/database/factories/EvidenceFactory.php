<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\Evidence;
use App\Infrastructure\Persistence\Eloquent\Models\ServiceReport;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\Evidence>
 */
class EvidenceFactory extends Factory
{
    protected $model = Evidence::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'service_report_id' => ServiceReport::factory(),
            'path' => 'evidence/'.fake()->uuid().'.jpg',
            'mime' => 'image/jpeg',
        ];
    }
}

