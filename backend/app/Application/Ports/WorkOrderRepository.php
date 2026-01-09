<?php

namespace App\Application\Ports;

interface WorkOrderRepository
{
    /**
     * @return array{id:int,appointment_id:int,assigned_user_id:?int,status:string,check_in_at:?string,check_out_at:?string}
     */
    public function createFromAppointment(int $appointmentId, ?int $assignedUserId = null): array;

    /**
     * @return array{id:int,appointment_id:int,status:string,check_in_at:?string,check_out_at:?string}|null
     */
    public function findByAppointmentId(int $appointmentId): ?array;

    /**
     * @return array{id:int,appointment_id:int,assigned_user_id:?int,status:string,check_in_at:?string,check_out_at:?string,check_in_lat:?string,check_in_lng:?string,check_out_lat:?string,check_out_lng:?string}|null
     */
    public function get(int $id): ?array;

    /**
     * @return array{id:int,appointment_id:int,assigned_user_id:?int,status:string,check_in_at:?string,check_out_at:?string}|null
     */
    public function update(int $id, array $data): ?array;

    public function countCreatedBetween(string $start, string $end): int;
}

