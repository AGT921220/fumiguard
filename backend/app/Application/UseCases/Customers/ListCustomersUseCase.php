<?php

namespace App\Application\UseCases\Customers;

use App\Application\Ports\CustomerRepository;

final readonly class ListCustomersUseCase
{
    public function __construct(private CustomerRepository $customers)
    {
    }

    public function execute(): array
    {
        return $this->customers->list();
    }
}

