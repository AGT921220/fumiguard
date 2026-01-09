<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\SiteRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Site;

final class EloquentSiteRepository extends TenantScopedRepository implements SiteRepository
{
    public function listByCustomer(int $customerId): array
    {
        return Site::query()
            ->where('tenant_id', $this->tenantId())
            ->where('customer_id', $customerId)
            ->orderBy('name')
            ->get(['id', 'customer_id', 'name', 'address_line1', 'city', 'state', 'postal_code', 'country', 'lat', 'lng', 'notes'])
            ->map(fn (Site $s) => $this->toArray($s))
            ->all();
    }

    public function get(int $id): ?array
    {
        $s = Site::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        return $s ? $this->toArray($s) : null;
    }

    public function create(array $data): array
    {
        $s = Site::query()->create([
            'tenant_id' => $this->tenantId(),
            'customer_id' => $data['customer_id'],
            'name' => $data['name'],
            'address_line1' => $data['address_line1'] ?? null,
            'address_line2' => $data['address_line2'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return $this->toArray($s);
    }

    public function update(int $id, array $data): ?array
    {
        $s = Site::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->first();

        if (! $s) {
            return null;
        }

        $s->fill($data);
        $s->save();

        return $this->toArray($s);
    }

    public function delete(int $id): bool
    {
        return (bool) Site::query()
            ->where('tenant_id', $this->tenantId())
            ->whereKey($id)
            ->delete();
    }

    private function toArray(Site $s): array
    {
        return [
            'id' => (int) $s->id,
            'customer_id' => (int) $s->customer_id,
            'name' => (string) $s->name,
            'address_line1' => $s->address_line1 !== null ? (string) $s->address_line1 : null,
            'address_line2' => $s->address_line2 !== null ? (string) $s->address_line2 : null,
            'city' => $s->city !== null ? (string) $s->city : null,
            'state' => $s->state !== null ? (string) $s->state : null,
            'postal_code' => $s->postal_code !== null ? (string) $s->postal_code : null,
            'country' => $s->country !== null ? (string) $s->country : null,
            'lat' => $s->lat !== null ? (string) $s->lat : null,
            'lng' => $s->lng !== null ? (string) $s->lng : null,
            'notes' => $s->notes !== null ? (string) $s->notes : null,
        ];
    }
}

