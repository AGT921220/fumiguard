<?php

namespace App\Application\Ports;

interface RecurrenceRuleRepository
{
    /**
     * @return array{id:int,frequency:string,day_of_month:int,interval_months:int,starts_on:?string}
     */
    public function create(array $data): array;

    /**
     * @return array{id:int,frequency:string,day_of_month:int,interval_months:int,starts_on:?string}|null
     */
    public function get(int $id): ?array;
}

