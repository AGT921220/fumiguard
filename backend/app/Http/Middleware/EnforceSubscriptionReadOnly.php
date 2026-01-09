<?php

namespace App\Http\Middleware;

use Closure;
use App\Application\Ports\SubscriptionRepository;
use App\Domain\Enums\SubscriptionStatus;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnforceSubscriptionReadOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow safe methods always.
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        // Allow billing endpoints even if inactive (so the tenant can recover).
        if ($request->is('api/v1/billing/*')) {
            return $next($request);
        }

        /** @var int|null $tenantId */
        $tenantId = $request->user()?->tenant_id;
        if (! is_int($tenantId) && ! is_numeric($tenantId)) {
            return $next($request);
        }

        /** @var SubscriptionRepository $subs */
        $subs = app(SubscriptionRepository::class);
        $sub = $subs->getForTenant((int) $tenantId);

        // Stripe is source of truth: if we don't know, default to read-only.
        $status = $sub['status'] ?? 'incomplete';
        if (SubscriptionStatus::isReadOnly((string) $status)) {
            throw new AccessDeniedHttpException('Suscripción inactiva: modo solo lectura.');
        }

        return $next($request);
    }
}
