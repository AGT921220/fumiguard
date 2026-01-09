<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Auth\UserContext;
use App\Application\Ports\AppointmentRepository;
use App\Application\Ports\CertificateFolioGenerator;
use App\Application\Ports\ChemicalUsageRepository;
use App\Application\Ports\CustomerRepository;
use App\Application\Ports\DocumentStorage;
use App\Application\Ports\EvidenceRepository;
use App\Application\Ports\PublicFileDataUriProvider;
use App\Application\Ports\ReportPdfGenerator;
use App\Application\Ports\ServicePlanRepository;
use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\SignatureRepository;
use App\Application\Ports\SiteRepository;
use App\Application\Ports\TenantRepository;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class EnsureReportDocumentsUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private WorkOrderRepository $workOrders,
        private AppointmentRepository $appointments,
        private CustomerRepository $customers,
        private SiteRepository $sites,
        private ServicePlanRepository $plans,
        private TenantRepository $tenants,
        private EvidenceRepository $evidences,
        private ChemicalUsageRepository $chemicals,
        private SignatureRepository $signatures,
        private PublicFileDataUriProvider $publicFiles,
        private CertificateFolioGenerator $folios,
        private ReportPdfGenerator $pdfs,
        private DocumentStorage $storage,
        private UserContext $userContext,
    ) {
    }

    /**
     * @return array{
     *   report_id:int,
     *   report_pdf_path:string,
     *   certificate_pdf_path:string,
     *   certificate_folio:string
     * }
     */
    public function execute(int $reportId): array
    {
        $report = $this->reports->getById($reportId);
        if ($report === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        $workOrder = $this->workOrders->get((int) $report['work_order_id']);
        if ($workOrder === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
        }

        $this->authorizeView($workOrder);

        $appointment = $this->appointments->get((int) $workOrder['appointment_id']);
        if ($appointment === null) {
            throw new NotFoundHttpException('Appointment no encontrado.');
        }

        $customer = $this->customers->get((int) $appointment['customer_id']);
        if ($customer === null) {
            throw new NotFoundHttpException('Customer no encontrado.');
        }

        $site = $this->sites->get((int) $appointment['site_id']);
        if ($site === null) {
            throw new NotFoundHttpException('Site no encontrado.');
        }

        $servicePlan = null;
        if (! empty($appointment['service_plan_id'])) {
            $servicePlan = $this->plans->get((int) $appointment['service_plan_id']);
        }

        $tenant = $this->tenants->findById((int) request()->user()?->tenant_id);
        if ($tenant === null) {
            throw new NotFoundHttpException('Tenant no encontrado.');
        }

        $certificateFolio = $report['certificate_folio'] ?? null;
        if ($certificateFolio === null) {
            $certificateFolio = $this->folios->nextFolio();
            $this->reports->update($reportId, ['certificate_folio' => $certificateFolio]);
            $report = $this->reports->getById($reportId) ?? $report;
        }

        $reportPdfPath = $report['report_pdf_path'] ?? null;
        $certificatePdfPath = $report['certificate_pdf_path'] ?? null;

        if ($reportPdfPath === null || $certificatePdfPath === null) {
            $evidences = $this->evidences->listByReport($reportId);
            $evidenceWithThumbs = array_map(function (array $e) {
                $thumb = $this->publicFiles->dataUriFor($e['path'], $e['mime'] ?? null);
                $e['thumbnail_data_uri'] = $thumb;
                return $e;
            }, $evidences);

            $chemicals = $this->chemicals->listByReport($reportId);
            $signatures = $this->signatures->listByReport($reportId);

            $issuedAt = CarbonImmutable::now()->toISOString();

            $payload = [
                'tenant' => ['id' => $tenant->id, 'name' => $tenant->name, 'slug' => $tenant->slug],
                'customer' => $customer,
                'site' => $site,
                'service_plan' => $servicePlan,
                'appointment' => $appointment,
                'work_order' => $workOrder,
                'report' => $report,
                'evidences' => $evidenceWithThumbs,
                'chemicals' => $chemicals,
                'signatures' => $signatures,
                'certificate' => ['folio' => $certificateFolio, 'issued_at' => $issuedAt],
            ];

            $reportPdf = $this->pdfs->generateServiceReport($payload);
            $certificatePdf = $this->pdfs->generateCertificate($payload);

            $baseDir = 'reports/'.$tenant->id.'/'.$reportId;
            $reportPdfPath = $baseDir.'/service-report.pdf';
            $certificatePdfPath = $baseDir.'/certificate-'.$certificateFolio.'.pdf';

            $this->storage->put($reportPdfPath, $reportPdf);
            $this->storage->put($certificatePdfPath, $certificatePdf);

            $this->reports->update($reportId, [
                'report_pdf_path' => $reportPdfPath,
                'certificate_pdf_path' => $certificatePdfPath,
            ]);
        }

        return [
            'report_id' => $reportId,
            'report_pdf_path' => (string) $reportPdfPath,
            'certificate_pdf_path' => (string) $certificatePdfPath,
            'certificate_folio' => (string) $certificateFolio,
        ];
    }

    private function authorizeView(array $workOrder): void
    {
        $role = $this->userContext->requireRole();

        if ($role === UserRole::TECHNICIAN) {
            $assigned = $workOrder['assigned_user_id'] ?? null;
            if ($assigned === null || (int) $assigned !== $this->userContext->requireUserId()) {
                throw new AccessDeniedHttpException('No autorizado.');
            }
        }
    }
}

