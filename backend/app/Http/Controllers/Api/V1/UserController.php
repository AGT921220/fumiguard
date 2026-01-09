<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Users\CreateTechnicianUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends Controller
{
    public function createTechnician(Request $request, CreateTechnicianUseCase $useCase)
    {
        $authUser = $request->user();

        if ($authUser?->role !== 'TENANT_ADMIN') {
            throw new AccessDeniedHttpException('No autorizado.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        return response()->json(
            $useCase->execute((int) $authUser->tenant_id, $data),
            201
        );
    }
}
