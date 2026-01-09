<?php

namespace App\Application\UseCases\Customers;

use App\Application\Ports\CustomerRepository;

final readonly class CreateCustomerUseCase
{
    public function __construct(private CustomerRepository $customers)
    {
    }

    public function execute(array $data): array
    {
        return $this->customers->create($data);
    }
}

