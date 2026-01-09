<?php

namespace App\Infrastructure\Providers;

use App\Application\Ports\AuthTokenService;
use App\Application\Ports\AppointmentRepository;
use App\Application\Ports\CertificateFolioGenerator;
use App\Application\Ports\CustomerRepository;
use App\Application\Ports\ChemicalUsageRepository;
use App\Application\Ports\DocumentStorage;
use App\Application\Ports\EvidenceRepository;
use App\Application\Ports\EvidenceStorage;
use App\Application\Ports\PublicFileDataUriProvider;
use App\Application\Ports\RecurrenceRuleRepository;
use App\Application\Ports\ReportPdfGenerator;
use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\ServicePlanRepository;
use App\Application\Ports\SignatureRepository;
use App\Application\Ports\SiteRepository;
use App\Application\Ports\TenantRepository;
use App\Application\Ports\UserRepository;
use App\Infrastructure\Auth\SanctumAuthTokenService;
use App\Application\Ports\WorkOrderRepository;
use App\Infrastructure\Documents\DompdfReportPdfGenerator;
use App\Infrastructure\Documents\LocalDocumentStorage;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAppointmentRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentChemicalUsageRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentCustomerRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentEvidenceRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentRecurrenceRuleRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentServiceReportRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentServicePlanRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentSignatureRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentSiteRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentTenantRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentWorkOrderRepository;
use App\Infrastructure\Storage\PublicDiskDataUriProvider;
use App\Infrastructure\Storage\PublicEvidenceStorage;
use App\Infrastructure\Tenancy\DbCertificateFolioGenerator;
use Illuminate\Support\ServiceProvider;

final class InfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(TenantRepository::class, EloquentTenantRepository::class);
        $this->app->bind(AuthTokenService::class, SanctumAuthTokenService::class);

        $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
        $this->app->bind(SiteRepository::class, EloquentSiteRepository::class);
        $this->app->bind(ServicePlanRepository::class, EloquentServicePlanRepository::class);

        $this->app->bind(RecurrenceRuleRepository::class, EloquentRecurrenceRuleRepository::class);
        $this->app->bind(AppointmentRepository::class, EloquentAppointmentRepository::class);
        $this->app->bind(WorkOrderRepository::class, EloquentWorkOrderRepository::class);
        $this->app->bind(ServiceReportRepository::class, EloquentServiceReportRepository::class);
        $this->app->bind(EvidenceRepository::class, EloquentEvidenceRepository::class);
        $this->app->bind(ChemicalUsageRepository::class, EloquentChemicalUsageRepository::class);
        $this->app->bind(SignatureRepository::class, EloquentSignatureRepository::class);
        $this->app->bind(EvidenceStorage::class, PublicEvidenceStorage::class);

        $this->app->bind(PublicFileDataUriProvider::class, PublicDiskDataUriProvider::class);
        $this->app->bind(CertificateFolioGenerator::class, DbCertificateFolioGenerator::class);
        $this->app->bind(ReportPdfGenerator::class, DompdfReportPdfGenerator::class);
        $this->app->bind(DocumentStorage::class, LocalDocumentStorage::class);
    }
}

