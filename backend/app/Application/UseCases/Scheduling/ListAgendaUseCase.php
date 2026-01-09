<?php

namespace App\Application\UseCases\Scheduling;

use App\Application\Ports\AppointmentRepository;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class ListAgendaUseCase
{
    public function __construct(private AppointmentRepository $appointments)
    {
    }

    /**
     * @return list<array{id:int,customer_id:int,site_id:int,scheduled_at:string,status:string}>
     */
    public function execute(string $view, string $dateIso): array
    {
        $date = CarbonImmutable::parse($dateIso)->startOfDay();

        if ($view === 'day') {
            $start = $date;
            $end = $date->endOfDay();
        } elseif ($view === 'week') {
            $start = $date->startOfWeek();
            $end = $date->endOfWeek();
        } else {
            throw new BadRequestHttpException('Vista inválida. Use day|week.');
        }

        return $this->appointments->listBetween($start->toDateTimeString(), $end->toDateTimeString());
    }
}

