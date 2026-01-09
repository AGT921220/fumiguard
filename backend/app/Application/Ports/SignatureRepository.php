<?php

namespace App\Application\Ports;

interface SignatureRepository
{
    /**
     * @param array{service_report_id:int,signed_by_name:string,signed_by_role?:?string,signature_data:string,signed_at:string} $data
     * @return array{id:int,service_report_id:int,signed_by_name:string,signed_by_role:?string,signed_at:string}
     */
    public function create(array $data): array;

    /**
     * @return list<array{id:int,service_report_id:int,signed_by_name:string,signed_by_role:?string,signed_at:string}>
     */
    public function listByReport(int $serviceReportId): array;
}

