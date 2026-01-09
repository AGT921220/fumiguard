<?php

namespace App\Application\UseCases\Sites;

use App\Application\Ports\CustomerRepository;
use App\Application\Ports\SiteRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ListSitesByCustomerUseCase
{
    public function __construct(
        private SiteRepository $sites,
        private CustomerRepository $customers,
    ) {
    }

    public function execute(int $customerId): array
    {
        if ($this->customers->get($customerId) === null) {
            throw new NotFoundHttpException('Customer no encontrado.');
        }

        return $this->sites->listByCustomer($customerId);
    }
}

