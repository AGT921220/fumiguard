<?php

namespace App\Application\Ports;

interface AppointmentRepository
{
    /**
     * @param array{
     *   customer_id:int,
     *   site_id:int,
     *   service_plan_id?:?int,
     *   recurrence_rule_id?:?int,
     *   scheduled_at:string,
     *   status?:string,
     *   notes?:?string
     * } $data
     * @return array{id:int,customer_id:int,site_id:int,service_plan_id:?int,recurrence_rule_id:?int,scheduled_at:string,status:string,notes:?string}
     */
    public function create(array $data): array;

    /**
     * @return array{id:int,customer_id:int,site_id:int,service_plan_id:?int,recurrence_rule_id:?int,scheduled_at:string,status:string,notes:?string}|null
     */
    public function get(int $id): ?array;

    /**
     * @return list<array{id:int,customer_id:int,site_id:int,scheduled_at:string,status:string}>
     */
    public function listBetween(string $startIso, string $endIso): array;
}

