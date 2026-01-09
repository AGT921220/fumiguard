<?php

namespace App\Application\Ports;

interface PublicFileDataUriProvider
{
    public function dataUriFor(string $path, ?string $mime): ?string;
}

