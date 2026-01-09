<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Billing\CreateCheckoutUseCase;
use App\Application\UseCases\Billing\CreatePortalUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function checkout(Request $request, CreateCheckoutUseCase $useCase)
    {
        $data = $request->validate([
            'plan_key' => ['required', 'in:BASIC,PRO,ENTERPRISE'],
        ]);

        return response()->json($useCase->execute($data['plan_key']));
    }

    public function portal(CreatePortalUseCase $useCase)
    {
        return response()->json($useCase->execute());
    }
}
