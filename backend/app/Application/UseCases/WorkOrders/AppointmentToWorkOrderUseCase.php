<?php

namespace App\Application\UseCases\WorkOrders;

use App\Application\Ports\AppointmentRepository;
use App\Application\Ports\WorkOrderRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class AppointmentToWorkOrderUseCase
{
    public function __construct(
        private AppointmentRepository $appointments,
        private WorkOrderRepository $workOrders,
    ) {
    }

    public function execute(int $appointmentId): array
    {
        if ($this->appointments->get($appointmentId) === null) {
            throw new NotFoundHttpException('Appointment no encontrado.');
        }

        $existing = $this->workOrders->findByAppointmentId($appointmentId);
        if ($existing !== null) {
            return $existing;
        }

        return $this->workOrders->createFromAppointment($appointmentId);
    }
}

