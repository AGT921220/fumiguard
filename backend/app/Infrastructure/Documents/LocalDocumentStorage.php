<?php

namespace App\Infrastructure\Documents;

use App\Application\Ports\DocumentStorage;
use Illuminate\Support\Facades\Storage;

final class LocalDocumentStorage implements DocumentStorage
{
    public function put(string $path, string $bytes): void
    {
        Storage::disk('local')->put($path, $bytes);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('local')->exists($path);
    }

    public function get(string $path): string
    {
        return Storage::disk('local')->get($path);
    }
}

