<?php

namespace App\Http\Middleware;

use Closure;
use App\Application\Auth\UserContext;
use App\Application\Tenancy\TenantContext;
use App\Domain\Enums\UserRole;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantFromAuthenticatedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && isset($user->tenant_id)) {
            /** @var TenantContext $tenantContext */
            $tenantContext = app(TenantContext::class);
            $tenantContext->setTenantId((int) $user->tenant_id);

            if (isset($user->id, $user->role)) {
                /** @var UserContext $userContext */
                $userContext = app(UserContext::class);
                $userContext->set((int) $user->id, UserRole::from((string) $user->role));
            }
        }

        return $next($request);
    }
}
