<?php

namespace App\Application\Ports;

interface ChemicalUsageRepository
{
    /**
     * @param array{service_report_id:int,chemical_name:string,quantity:float,unit:string} $data
     * @return array{id:int,service_report_id:int,chemical_name:string,quantity:string,unit:string}
     */
    public function create(array $data): array;

    /**
     * @return list<array{id:int,service_report_id:int,chemical_name:string,quantity:string,unit:string}>
     */
    public function listByReport(int $serviceReportId): array;
}

