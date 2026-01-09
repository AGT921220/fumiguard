<?php

namespace Tests\Feature\Pdf;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportPdfDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_download_report_and_certificate_pdfs(): void
    {
        Storage::fake('public');

        $this->seed();

        $login = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
        ])->assertOk();

        $token = (string) $login->json('token');
        $auth = ['Authorization' => 'Bearer '.$token];

        $customer = $this->withHeaders($auth)->postJson('/api/v1/customers', [
            'name' => 'Cliente PDF',
        ])->assertStatus(201);

        $customerId = (int) $customer->json('id');

        $site = $this->withHeaders($auth)->postJson('/api/v1/sites', [
            'customer_id' => $customerId,
            'name' => 'Sitio PDF',
            'city' => 'CDMX',
        ])->assertStatus(201);

        $siteId = (int) $site->json('id');

        $plan = $this->withHeaders($auth)->postJson('/api/v1/service-plans', [
            'name' => 'Plan PDF',
            'certificate_template' => ['title' => 'Certificado'],
        ])->assertStatus(201);

        $planId = (int) $plan->json('id');

        $appointment = $this->withHeaders($auth)->postJson('/api/v1/appointments', [
            'customer_id' => $customerId,
            'site_id' => $siteId,
            'service_plan_id' => $planId,
            'scheduled_at' => now()->toISOString(),
        ])->assertStatus(201);

        $appointmentId = (int) $appointment->json('id');

        $workOrder = $this->withHeaders($auth)
            ->postJson('/api/v1/appointments/'.$appointmentId.'/work-order')
            ->assertStatus(201);

        $workOrderId = (int) $workOrder->json('id');

        $this->withHeaders($auth)->postJson('/api/v1/work-orders/'.$workOrderId.'/check-in', [
            'lat' => 19.4326,
            'lng' => -99.1332,
        ])->assertOk();

        $this->withHeaders($auth)
            ->postJson('/api/v1/work-orders/'.$workOrderId.'/report/start')
            ->assertStatus(201);

        $this->withHeaders($auth)
            ->putJson('/api/v1/work-orders/'.$workOrderId.'/report/checklist', [
                'checklist' => [['key' => 'ok', 'value' => true]],
                'notes' => 'PDF OK',
            ])
            ->assertOk();

        $this->withHeaders($auth)
            ->post('/api/v1/work-orders/'.$workOrderId.'/report/evidence', [
                'file' => UploadedFile::fake()->image('evidence.jpg'),
            ])
            ->assertStatus(201);

        $final = $this->withHeaders($auth)
            ->postJson('/api/v1/work-orders/'.$workOrderId.'/report/finalize')
            ->assertOk();

        $reportId = (int) $final->json('id');
        $this->assertGreaterThan(0, $reportId);

        $pdf = $this->withHeaders($auth)->get('/api/v1/reports/'.$reportId.'/pdf');
        $pdf->assertOk();
        $pdf->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdf->streamedContent());

        $cert = $this->withHeaders($auth)->get('/api/v1/reports/'.$reportId.'/certificate');
        $cert->assertOk();
        $cert->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $cert->streamedContent());
    }
}

