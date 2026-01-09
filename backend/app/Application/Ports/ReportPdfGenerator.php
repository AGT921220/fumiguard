<?php

namespace App\Application\Ports;

interface ReportPdfGenerator
{
    /**
     * @param array<string,mixed> $payload
     */
    public function generateServiceReport(array $payload): string;

    /**
     * @param array<string,mixed> $payload
     */
    public function generateCertificate(array $payload): string;
}

