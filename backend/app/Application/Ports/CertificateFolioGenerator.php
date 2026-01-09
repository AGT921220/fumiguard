<?php

namespace App\Application\Ports;

interface CertificateFolioGenerator
{
    public function nextFolio(): string;
}

