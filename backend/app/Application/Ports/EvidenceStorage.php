<?php

namespace App\Application\Ports;

use Illuminate\Http\UploadedFile;

interface EvidenceStorage
{
    /**
     * @return array{path:string,mime:?string}
     */
    public function storeEvidence(UploadedFile $file): array;
}

