<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Billing\HandleStripeWebhookUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, HandleStripeWebhookUseCase $useCase)
    {
        $payload = $request->getContent();
        $sig = (string) $request->header('Stripe-Signature', '');

        $useCase->execute($payload, $sig);

        return response()->json(['ok' => true]);
    }
}
