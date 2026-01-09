<?php

namespace App\Application\UseCases\Sites;

use App\Application\Ports\CustomerRepository;
use App\Application\Ports\SiteRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CreateSiteUseCase
{
    public function __construct(
        private SiteRepository $sites,
        private CustomerRepository $customers,
    ) {
    }

    public function execute(array $data): array
    {
        // Enforce tenant boundaries via tenant-scoped customer lookup.
        if ($this->customers->get($data['customer_id']) === null) {
            throw new NotFoundHttpException('Customer no encontrado.');
        }

        return $this->sites->create($data);
    }
}

