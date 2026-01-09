<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\ServicePlan;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\ServicePlan>
 */
class ServicePlanFactory extends Factory
{
    protected $model = ServicePlan::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => 'Plan '.fake()->word(),
            'checklist_template' => [
                ['key' => 'area_perimeter', 'label' => 'Revisar perímetro', 'type' => 'boolean'],
                ['key' => 'traps', 'label' => 'Revisar trampas', 'type' => 'boolean'],
            ],
            'certificate_template' => [
                'title' => 'Certificado de servicio',
                'fields' => ['customer_name', 'site_name', 'date'],
            ],
            'is_active' => true,
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => ['tenant_id' => $tenant->id]);
    }
}

