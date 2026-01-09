<?php

namespace App\Application\Ports;

interface ServiceReportRepository
{
    /**
     * @return array{id:int,work_order_id:int,status:string,locked:bool,started_at:string,finalized_at:?string,checklist:mixed,notes:?string}
     */
    public function createForWorkOrder(int $workOrderId): array;

    /**
     * @return array{id:int,work_order_id:int,status:string,locked:bool,started_at:string,finalized_at:?string,checklist:mixed,notes:?string}|null
     */
    public function getByWorkOrderId(int $workOrderId): ?array;

    /**
     * @return array{id:int,work_order_id:int,status:string,locked:bool,started_at:string,finalized_at:?string,checklist:mixed,notes:?string}|null
     */
    public function update(int $id, array $data): ?array;
}

