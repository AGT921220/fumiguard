<?php

namespace App\Application\Ports;

use App\Domain\Entities\User;

interface UserRepository
{
    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;
}

