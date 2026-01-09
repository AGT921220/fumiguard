<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Scheduling\CreateAppointmentUseCase;
use App\Application\UseCases\Scheduling\CreateRecurrenceUseCase;
use App\Application\UseCases\Scheduling\ListAgendaUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchedulingController extends Controller
{
    public function createRecurrence(Request $request, CreateRecurrenceUseCase $useCase)
    {
        $data = $request->validate([
            'frequency' => ['required', 'string'],
            'day_of_month' => ['nullable', 'integer', 'min:1', 'max:28'],
            'interval_months' => ['nullable', 'integer', 'min:1', 'max:12'],
            'starts_on' => ['nullable', 'date'],
        ]);

        return response()->json($useCase->execute($data), 201);
    }

    public function createAppointment(Request $request, CreateAppointmentUseCase $useCase)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'site_id' => ['required', 'integer'],
            'service_plan_id' => ['nullable', 'integer'],
            'recurrence_rule_id' => ['nullable', 'integer'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        return response()->json($useCase->execute($data), 201);
    }

    public function agenda(Request $request, ListAgendaUseCase $useCase)
    {
        $data = $request->validate([
            'view' => ['required', 'in:day,week'],
            'date' => ['required', 'date'],
        ]);

        return response()->json($useCase->execute($data['view'], $data['date']));
    }
}
