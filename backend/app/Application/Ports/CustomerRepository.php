<?php

namespace App\Application\Ports;

interface CustomerRepository
{
    /**
     * @return list<array{id:int,name:string,email:?string,phone:?string,notes:?string}>
     */
    public function list(): array;

    /**
     * @return array{id:int,name:string,email:?string,phone:?string,notes:?string}|null
     */
    public function get(int $id): ?array;

    /**
     * @param array{name:string,email?:?string,phone?:?string,notes?:?string} $data
     * @return array{id:int,name:string,email:?string,phone:?string,notes:?string}
     */
    public function create(array $data): array;

    /**
     * @param array{name?:string,email?:?string,phone?:?string,notes?:?string} $data
     * @return array{id:int,name:string,email:?string,phone:?string,notes:?string}|null
     */
    public function update(int $id, array $data): ?array;

    public function delete(int $id): bool;
}

