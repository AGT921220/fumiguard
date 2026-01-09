<?php

namespace App\Application\UseCases\Customers;

use App\Application\Ports\CustomerRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GetCustomerUseCase
{
    public function __construct(private CustomerRepository $customers)
    {
    }

    public function execute(int $id): array
    {
        $customer = $this->customers->get($id);

        if ($customer === null) {
            throw new NotFoundHttpException('Customer no encontrado.');
        }

        return $customer;
    }
}

