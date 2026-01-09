<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\CustomerRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Customer;

final class EloquentCustomerRepository extends TenantScopedRepository implements CustomerRepository
{
    public function list(): array
    {
        return Customer::query()
            ->where('tenant_id', $this->tenantId())
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'notes'])
            ->map(fn (Customer $c) => $this->toArray($c))
            ->all();
    }

    public function get(int $id): ?array
    {
        $c = Customer::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first(['id', 'name', 'email', 'phone', 'notes']);

        return $c ? $this->toArray($c) : null;
    }

    public function create(array $data): array
    {
        $c = Customer::query()->create([
            'tenant_id' => $this->tenantId(),
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return $this->toArray($c);
    }

    public function update(int $id, array $data): ?array
    {
        $c = Customer::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        if (! $c) {
            return null;
        }

        $c->fill($data);
        $c->save();

        return $this->toArray($c);
    }

    public function delete(int $id): bool
    {
        return (bool) Customer::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->delete();
    }

    private function toArray(Customer $c): array
    {
        return [
            'id' => (int) $c->id,
            'name' => (string) $c->name,
            'email' => $c->email !== null ? (string) $c->email : null,
            'phone' => $c->phone !== null ? (string) $c->phone : null,
            'notes' => $c->notes !== null ? (string) $c->notes : null,
        ];
    }
}

