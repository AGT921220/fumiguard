<?php

namespace App\Application\UseCases\Sites;

use App\Application\Ports\SiteRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GetSiteUseCase
{
    public function __construct(private SiteRepository $sites)
    {
    }

    public function execute(int $id): array
    {
        $site = $this->sites->get($id);

        if ($site === null) {
            throw new NotFoundHttpException('Site no encontrado.');
        }

        return $site;
    }
}

