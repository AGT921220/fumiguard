<?php

namespace App\Application\UseCases\Customers;

use App\Application\Ports\CustomerRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UpdateCustomerUseCase
{
    public function __construct(private CustomerRepository $customers)
    {
    }

    public function execute(int $id, array $data): array
    {
        $updated = $this->customers->update($id, $data);

        if ($updated === null) {
            throw new NotFoundHttpException('Customer no encontrado.');
        }

        return $updated;
    }
}

