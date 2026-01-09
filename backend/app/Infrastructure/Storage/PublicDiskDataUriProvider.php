<?php

namespace App\Infrastructure\Storage;

use App\Application\Ports\PublicFileDataUriProvider;
use Illuminate\Support\Facades\Storage;

final class PublicDiskDataUriProvider implements PublicFileDataUriProvider
{
    public function dataUriFor(string $path, ?string $mime): ?string
    {
        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        $bytes = Storage::disk('public')->get($path);
        $b64 = base64_encode($bytes);
        $type = $mime ?: 'application/octet-stream';

        return 'data:'.$type.';base64,'.$b64;
    }
}

