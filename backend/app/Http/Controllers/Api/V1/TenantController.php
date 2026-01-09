<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Tenant\GetCurrentTenantUseCase;
use App\Http\Controllers\Controller;

class TenantController extends Controller
{
    public function current(GetCurrentTenantUseCase $useCase)
    {
        return response()->json($useCase->execute());
    }
}
