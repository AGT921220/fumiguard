<?php

namespace App\Application\UseCases\Sites;

use App\Application\Ports\SiteRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class DeleteSiteUseCase
{
    public function __construct(private SiteRepository $sites)
    {
    }

    public function execute(int $id): void
    {
        if (! $this->sites->delete($id)) {
            throw new NotFoundHttpException('Site no encontrado.');
        }
    }
}

