<?php

namespace App\Application\Ports;

interface ServicePlanRepository
{
    /**
     * @return list<array{id:int,name:string,is_active:bool,checklist_template:mixed,certificate_template:mixed}>
     */
    public function list(): array;

    /**
     * @return array{id:int,name:string,is_active:bool,checklist_template:mixed,certificate_template:mixed}|null
     */
    public function get(int $id): ?array;

    /**
     * @param array{name:string,checklist_template?:mixed,certificate_template?:mixed,is_active?:bool} $data
     * @return array{id:int,name:string,is_active:bool,checklist_template:mixed,certificate_template:mixed}
     */
    public function create(array $data): array;

    /**
     * @param array{name?:string,checklist_template?:mixed,certificate_template?:mixed,is_active?:bool} $data
     * @return array{id:int,name:string,is_active:bool,checklist_template:mixed,certificate_template:mixed}|null
     */
    public function update(int $id, array $data): ?array;

    public function delete(int $id): bool;
}

