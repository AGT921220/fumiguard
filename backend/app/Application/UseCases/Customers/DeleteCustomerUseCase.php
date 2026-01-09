<?php

namespace App\Application\UseCases\Customers;

use App\Application\Ports\CustomerRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class DeleteCustomerUseCase
{
    public function __construct(private CustomerRepository $customers)
    {
    }

    public function execute(int $id): void
    {
        if (! $this->customers->delete($id)) {
            throw new NotFoundHttpException('Customer no encontrado.');
        }
    }
}

