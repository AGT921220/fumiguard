<?php

namespace App\Application\UseCases\Scheduling;

use App\Application\Ports\RecurrenceRuleRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class CreateRecurrenceUseCase
{
    public function __construct(private RecurrenceRuleRepository $rules)
    {
    }

    public function execute(array $data): array
    {
        $frequency = $data['frequency'];
        if (! in_array($frequency, ['MONTHLY', 'QUARTERLY'], true)) {
            throw new BadRequestHttpException('Frecuencia inválida.');
        }

        $interval = $frequency === 'QUARTERLY' ? 3 : 1;

        return $this->rules->create([
            'frequency' => $frequency,
            'day_of_month' => $data['day_of_month'] ?? 1,
            'interval_months' => $data['interval_months'] ?? $interval,
            'starts_on' => $data['starts_on'] ?? null,
        ]);
    }
}

