<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Auth\GetMeUseCase;
use App\Application\UseCases\Auth\LoginUseCase;
use App\Application\UseCases\Auth\LogoutUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, LoginUseCase $login)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        return response()->json($login->execute(
            email: $validated['email'],
            password: $validated['password'],
        ));
    }

    public function logout(Request $request, LogoutUseCase $logout)
    {
        $token = $request->user()?->currentAccessToken();

        $logout->execute($token?->id);

        return response()->json(['ok' => true]);
    }

    public function me(Request $request, GetMeUseCase $me)
    {
        return response()->json($me->execute($request->user()?->id));
    }
}
