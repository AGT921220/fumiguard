<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\Customer;
use App\Infrastructure\Persistence\Eloquent\Models\Site;
use App\Infrastructure\Persistence\Eloquent\Models\ServicePlan;
use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Fumiguard Demo',
            'slug' => 'fumiguard-demo',
        ]);

        User::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Demo',
            'email' => 'admin@demo.test',
            'password' => 'password',
            'role' => 'TENANT_ADMIN',
        ]);

        User::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Dispatcher Demo',
            'email' => 'dispatcher@demo.test',
            'password' => 'password',
            'role' => 'DISPATCHER',
        ]);

        $customer = Customer::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Cliente Demo',
            'email' => 'cliente@demo.test',
            'phone' => '+52 55 0000 0000',
        ]);

        $site = Site::query()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'name' => 'Sucursal Demo',
            'city' => 'CDMX',
            'country' => 'MX',
        ]);

        $plan = ServicePlan::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Plan Demo',
            'checklist_template' => [
                ['key' => 'perimeter', 'label' => 'Revisar perímetro', 'type' => 'boolean'],
            ],
            'certificate_template' => [
                'title' => 'Certificado de servicio (Demo)',
            ],
            'is_active' => true,
        ]);

        Appointment::query()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'site_id' => $site->id,
            'service_plan_id' => $plan->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'SCHEDULED',
        ]);
    }
}
