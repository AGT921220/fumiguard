<?php

namespace App\Application\Ports;

interface DocumentStorage
{
    public function put(string $path, string $bytes): void;

    public function exists(string $path): bool;

    public function get(string $path): string;
}

