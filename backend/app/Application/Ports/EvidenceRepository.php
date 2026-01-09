<?php

namespace App\Application\Ports;

interface EvidenceRepository
{
    /**
     * @param array{service_report_id:int,path:string,mime?:?string} $data
     * @return array{id:int,service_report_id:int,path:string,mime:?string}
     */
    public function create(array $data): array;

    /**
     * @return list<array{id:int,service_report_id:int,path:string,mime:?string}>
     */
    public function listByReport(int $serviceReportId): array;
}

