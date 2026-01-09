<?php

namespace App\Domain\Entities;

final readonly class Tenant
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
    ) {
    }
}

