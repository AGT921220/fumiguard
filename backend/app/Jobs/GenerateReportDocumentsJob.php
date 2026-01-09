<?php

namespace App\Jobs;

use App\Application\UseCases\ServiceReports\EnsureReportDocumentsUseCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReportDocumentsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $reportId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(EnsureReportDocumentsUseCase $useCase): void
    {
        $useCase->execute($this->reportId);
    }
}
