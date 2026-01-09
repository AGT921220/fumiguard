<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\WorkOrders\AppointmentToWorkOrderUseCase;
use App\Application\UseCases\WorkOrders\CheckInUseCase;
use App\Application\UseCases\WorkOrders\CheckOutUseCase;
use App\Application\UseCases\WorkOrders\UpdateStatusUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function fromAppointment(int $appointmentId, AppointmentToWorkOrderUseCase $useCase)
    {
        return response()->json($useCase->execute($appointmentId), 201);
    }

    public function updateStatus(int $id, Request $request, UpdateStatusUseCase $useCase)
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);

        return response()->json($useCase->execute($id, $data['status']));
    }

    public function checkIn(int $id, Request $request, CheckInUseCase $useCase)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'at' => ['nullable', 'date'],
        ]);

        return response()->json($useCase->execute(
            workOrderId: $id,
            lat: (float) $data['lat'],
            lng: (float) $data['lng'],
            atIso: $data['at'] ?? null,
        ));
    }

    public function checkOut(int $id, Request $request, CheckOutUseCase $useCase)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'at' => ['nullable', 'date'],
        ]);

        return response()->json($useCase->execute(
            workOrderId: $id,
            lat: (float) $data['lat'],
            lng: (float) $data['lng'],
            atIso: $data['at'] ?? null,
        ));
    }
}
