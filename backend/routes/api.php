<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TenantController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\BillingController;
use App\Http\Controllers\Api\V1\ReportDocumentController;
use App\Http\Controllers\Api\V1\SchedulingController;
use App\Http\Controllers\Api\V1\ServicePlanController;
use App\Http\Controllers\Api\V1\ServiceReportController;
use App\Http\Controllers\Api\V1\SiteController;
use App\Http\Controllers\Api\V1\StripeWebhookController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\WorkOrderController;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'backend',
            'timestamp' => now()->toISOString(),
        ]);
    });

    Route::post('/login', [AuthController::class, 'login']);

    // Stripe webhooks (no auth)
    Route::post('/billing/webhook', [StripeWebhookController::class, 'handle']);

    Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::get('/tenant', [TenantController::class, 'current']);

        // Billing (allowed even if subscription is inactive)
        Route::post('/billing/checkout', [BillingController::class, 'checkout']);
        Route::post('/billing/portal', [BillingController::class, 'portal']);
    });

    // Everything below is subscription-gated (read-only if inactive).
    Route::middleware(['auth:sanctum', 'tenant', 'sub_active'])->group(function () {
        // Users
        Route::post('/users/technicians', [UserController::class, 'createTechnician']);

        // Customers
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::get('/customers/{id}', [CustomerController::class, 'show']);
        Route::put('/customers/{id}', [CustomerController::class, 'update']);
        Route::patch('/customers/{id}', [CustomerController::class, 'update']);
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

        // Sites
        Route::get('/customers/{customerId}/sites', [SiteController::class, 'indexByCustomer']);
        Route::post('/sites', [SiteController::class, 'store']);
        Route::get('/sites/{id}', [SiteController::class, 'show']);
        Route::put('/sites/{id}', [SiteController::class, 'update']);
        Route::patch('/sites/{id}', [SiteController::class, 'update']);
        Route::delete('/sites/{id}', [SiteController::class, 'destroy']);

        // Service plans
        Route::get('/service-plans', [ServicePlanController::class, 'index']);
        Route::post('/service-plans', [ServicePlanController::class, 'store']);
        Route::get('/service-plans/{id}', [ServicePlanController::class, 'show']);
        Route::put('/service-plans/{id}', [ServicePlanController::class, 'update']);
        Route::patch('/service-plans/{id}', [ServicePlanController::class, 'update']);
        Route::delete('/service-plans/{id}', [ServicePlanController::class, 'destroy']);

        // Scheduling
        Route::post('/recurrences', [SchedulingController::class, 'createRecurrence']);
        Route::post('/appointments', [SchedulingController::class, 'createAppointment']);
        Route::get('/agenda', [SchedulingController::class, 'agenda']);

        // Work orders
        Route::post('/appointments/{appointmentId}/work-order', [WorkOrderController::class, 'fromAppointment']);
        Route::patch('/work-orders/{id}/status', [WorkOrderController::class, 'updateStatus']);
        Route::post('/work-orders/{id}/check-in', [WorkOrderController::class, 'checkIn']);
        Route::post('/work-orders/{id}/check-out', [WorkOrderController::class, 'checkOut']);

        // Service reports
        Route::post('/work-orders/{workOrderId}/report/start', [ServiceReportController::class, 'start']);
        Route::put('/work-orders/{workOrderId}/report/checklist', [ServiceReportController::class, 'saveChecklist']);
        Route::post('/work-orders/{workOrderId}/report/chemicals', [ServiceReportController::class, 'addChemical']);
        Route::post('/work-orders/{workOrderId}/report/evidence', [ServiceReportController::class, 'uploadEvidence']);
        Route::post('/work-orders/{workOrderId}/report/signature', [ServiceReportController::class, 'captureSignature']);
        Route::post('/work-orders/{workOrderId}/report/finalize', [ServiceReportController::class, 'finalize']);

        // Report documents (PDF)
        Route::get('/reports/{id}/pdf', [ReportDocumentController::class, 'serviceReport']);
        Route::get('/reports/{id}/certificate', [ReportDocumentController::class, 'certificate']);
    });
});

