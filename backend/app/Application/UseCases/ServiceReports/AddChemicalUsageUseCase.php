<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Ports\ChemicalUsageRepository;
use App\Application\Ports\ServiceReportRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class AddChemicalUsageUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private ChemicalUsageRepository $chemicals,
    ) {
    }

    public function execute(int $workOrderId, array $data): array
    {
        $report = $this->reports->getByWorkOrderId($workOrderId);
        if ($report === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        if ($report['locked']) {
            throw new BadRequestHttpException('ServiceReport está finalizado y bloqueado.');
        }

        return $this->chemicals->create([
            'service_report_id' => $report['id'],
            'chemical_name' => $data['chemical_name'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
        ]);
    }
}

