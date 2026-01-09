<?php

namespace App\Application\UseCases\Sites;

use App\Application\Ports\SiteRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UpdateSiteUseCase
{
    public function __construct(private SiteRepository $sites)
    {
    }

    public function execute(int $id, array $data): array
    {
        $updated = $this->sites->update($id, $data);

        if ($updated === null) {
            throw new NotFoundHttpException('Site no encontrado.');
        }

        return $updated;
    }
}

