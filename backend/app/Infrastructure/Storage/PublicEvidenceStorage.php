<?php

namespace App\Infrastructure\Storage;

use App\Application\Ports\EvidenceStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

final class PublicEvidenceStorage implements EvidenceStorage
{
    public function storeEvidence(UploadedFile $file): array
    {
        $ext = $file->getClientOriginalExtension() ?: 'bin';
        $name = Str::uuid().'.'.$ext;

        $path = $file->storeAs('evidence', $name, ['disk' => 'public']);

        return [
            'path' => $path,
            'mime' => $file->getClientMimeType(),
        ];
    }
}

