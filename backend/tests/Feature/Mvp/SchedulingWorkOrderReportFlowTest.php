<?php

namespace Tests\Feature\Mvp;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchedulingWorkOrderReportFlowTest extends TestCase
{
    use RefreshDatabase;

    private function token(): string
    {
        $this->seed();

        $login = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
        ])->assertOk();

        return (string) $login->json('token');
    }

    public function test_end_to_end_flow(): void
    {
        Storage::fake('public');

        $token = $this->token();
        $auth = ['Authorization' => 'Bearer '.$token];

        $customer = $this->withHeaders($auth)->postJson('/api/v1/customers', [
            'name' => 'Cliente 1',
        ])->assertStatus(201);

        $customerId = (int) $customer->json('id');

        $site = $this->withHeaders($auth)->postJson('/api/v1/sites', [
            'customer_id' => $customerId,
            'name' => 'Sucursal Centro',
            'city' => 'CDMX',
        ])->assertStatus(201);

        $siteId = (int) $site->json('id');

        $plan = $this->withHeaders($auth)->postJson('/api/v1/service-plans', [
            'name' => 'Plan Mensual',
            'checklist_template' => [['key' => 'ok', 'label' => 'OK', 'type' => 'boolean']],
        ])->assertStatus(201);

        $planId = (int) $plan->json('id');

        $appointment = $this->withHeaders($auth)->postJson('/api/v1/appointments', [
            'customer_id' => $customerId,
            'site_id' => $siteId,
            'service_plan_id' => $planId,
            'scheduled_at' => now()->toISOString(),
        ])->assertStatus(201);

        $appointmentId = (int) $appointment->json('id');

        $this->withHeaders($auth)->getJson('/api/v1/agenda?view=day&date='.now()->toDateString())
            ->assertOk()
            ->assertJsonFragment(['id' => $appointmentId]);

        $workOrder = $this->withHeaders($auth)
            ->postJson('/api/v1/appointments/'.$appointmentId.'/work-order')
            ->assertStatus(201);

        $workOrderId = (int) $workOrder->json('id');

        $this->withHeaders($auth)
            ->postJson('/api/v1/work-orders/'.$workOrderId.'/check-in', [
                'lat' => 19.4326,
                'lng' => -99.1332,
            ])
            ->assertOk()
            ->assertJsonFragment(['status' => 'IN_PROGRESS']);

        $this->withHeaders($auth)
            ->postJson('/api/v1/work-orders/'.$workOrderId.'/report/start')
            ->assertStatus(201)
            ->assertJsonFragment(['work_order_id' => $workOrderId]);

        $this->withHeaders($auth)
            ->putJson('/api/v1/work-orders/'.$workOrderId.'/report/checklist', [
                'checklist' => [['key' => 'ok', 'value' => true]],
                'notes' => 'Sin novedades.',
            ])
            ->assertOk()
            ->assertJsonFragment(['notes' => 'Sin novedades.']);

        $this->withHeaders($auth)
            ->postJson('/api/v1/work-orders/'.$workOrderId.'/report/chemicals', [
                'chemical_name' => 'Gel X',
                'quantity' => 1.5,
                'unit' => 'l',
            ])
            ->assertStatus(201)
            ->assertJsonFragment(['chemical_name' => 'Gel X']);

        $this->withHeaders($auth)
            ->postJson('/api/v1/work-orders/'.$workOrderId.'/report/signature', [
                'signed_by_name' => 'Cliente',
                'signed_by_role' => 'CLIENT',
                'signature_data' => 'base64signature',
            ])
            ->assertStatus(201)
            ->assertJsonFragment(['signed_by_name' => 'Cliente']);

        $this->withHeaders($auth)
            ->post('/api/v1/work-orders/'.$workOrderId.'/report/evidence', [
                'file' => UploadedFile::fake()->image('evidence.jpg'),
            ])
            ->assertStatus(201);

        $final = $this->withHeaders($auth)
            ->postJson('/api/v1/work-orders/'.$workOrderId.'/report/finalize')
            ->assertOk()
            ->assertJsonFragment(['locked' => true, 'status' => 'FINAL']);

        $this->assertTrue((bool) $final->json('locked'));

        // Intentar editar después de finalizado debe fallar con el shape de error.
        $this->withHeaders($auth)
            ->putJson('/api/v1/work-orders/'.$workOrderId.'/report/checklist', [
                'checklist' => [['key' => 'ok', 'value' => false]],
                'notes' => 'Intento inválido.',
            ])
            ->assertStatus(400)
            ->assertJsonStructure(['message', 'errors', 'trace_id']);
    }
}

