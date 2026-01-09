<?php

namespace App\Application\UseCases\Scheduling;

use App\Application\Ports\AppointmentRepository;
use App\Application\Ports\CustomerRepository;
use App\Application\Ports\RecurrenceRuleRepository;
use App\Application\Ports\ServicePlanRepository;
use App\Application\Ports\SiteRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CreateAppointmentUseCase
{
    public function __construct(
        private AppointmentRepository $appointments,
        private CustomerRepository $customers,
        private SiteRepository $sites,
        private ServicePlanRepository $plans,
        private RecurrenceRuleRepository $rules,
    ) {
    }

    public function execute(array $data): array
    {
        if ($this->customers->get($data['customer_id']) === null) {
            throw new NotFoundHttpException('Customer no encontrado.');
        }

        $site = $this->sites->get($data['site_id']);
        if ($site === null) {
            throw new NotFoundHttpException('Site no encontrado.');
        }

        if ((int) $site['customer_id'] !== (int) $data['customer_id']) {
            throw new NotFoundHttpException('Site no encontrado.');
        }

        if (isset($data['service_plan_id']) && $data['service_plan_id'] !== null) {
            if ($this->plans->get((int) $data['service_plan_id']) === null) {
                throw new NotFoundHttpException('ServicePlan no encontrado.');
            }
        }

        if (isset($data['recurrence_rule_id']) && $data['recurrence_rule_id'] !== null) {
            if ($this->rules->get((int) $data['recurrence_rule_id']) === null) {
                throw new NotFoundHttpException('RecurrenceRule no encontrado.');
            }
        }

        return $this->appointments->create($data);
    }
}

