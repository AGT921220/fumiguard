<?php

namespace App\Application\UseCases\Users;

use App\Application\Ports\TenantUserAdminRepository;
use App\Application\UseCases\Billing\EnforcePlanLimits;
use App\Domain\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class CreateTechnicianUseCase
{
    public function __construct(
        private TenantUserAdminRepository $users,
        private EnforcePlanLimits $limits,
    ) {
    }

    public function execute(int $tenantId, array $data): array
    {
        $limits = $this->limits->limitsForTenant($tenantId);
        $max = $limits['max_technicians'];

        if ($max !== null) {
            $current = $this->users->countTechnicians($tenantId);
            if ($current >= $max) {
                throw new BadRequestHttpException('Límite de técnicos alcanzado.');
            }
        }

        return $this->users->createUser($tenantId, [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::TECHNICIAN->value,
        ]);
    }
}

