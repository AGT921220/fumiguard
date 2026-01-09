<?php

namespace App\Application\Ports;

interface SiteRepository
{
    /**
     * @return list<array{id:int,customer_id:int,name:string,address_line1:?string,city:?string,state:?string,postal_code:?string,country:?string,lat:?string,lng:?string,notes:?string}>
     */
    public function listByCustomer(int $customerId): array;

    /**
     * @return array{id:int,customer_id:int,name:string,address_line1:?string,address_line2:?string,city:?string,state:?string,postal_code:?string,country:?string,lat:?string,lng:?string,notes:?string}|null
     */
    public function get(int $id): ?array;

    /**
     * @param array{customer_id:int,name:string,address_line1?:?string,address_line2?:?string,city?:?string,state?:?string,postal_code?:?string,country?:?string,lat?:?float,lng?:?float,notes?:?string} $data
     * @return array{id:int,customer_id:int,name:string,address_line1:?string,address_line2:?string,city:?string,state:?string,postal_code:?string,country:?string,lat:?string,lng:?string,notes:?string}
     */
    public function create(array $data): array;

    /**
     * @param array{name?:string,address_line1?:?string,address_line2?:?string,city?:?string,state?:?string,postal_code?:?string,country?:?string,lat?:?float,lng?:?float,notes?:?string} $data
     * @return array{id:int,customer_id:int,name:string,address_line1:?string,address_line2:?string,city:?string,state:?string,postal_code:?string,country:?string,lat:?string,lng:?string,notes:?string}|null
     */
    public function update(int $id, array $data): ?array;

    public function delete(int $id): bool;
}

