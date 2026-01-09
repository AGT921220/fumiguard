<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Customers\CreateCustomerUseCase;
use App\Application\UseCases\Customers\DeleteCustomerUseCase;
use App\Application\UseCases\Customers\GetCustomerUseCase;
use App\Application\UseCases\Customers\ListCustomersUseCase;
use App\Application\UseCases\Customers\UpdateCustomerUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(ListCustomersUseCase $useCase)
    {
        return response()->json($useCase->execute());
    }

    public function show(int $id, GetCustomerUseCase $useCase)
    {
        return response()->json($useCase->execute($id));
    }

    public function store(Request $request, CreateCustomerUseCase $useCase)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        return response()->json($useCase->execute($data), 201);
    }

    public function update(int $id, Request $request, UpdateCustomerUseCase $useCase)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        return response()->json($useCase->execute($id, $data));
    }

    public function destroy(int $id, DeleteCustomerUseCase $useCase)
    {
        $useCase->execute($id);

        return response()->json(['ok' => true]);
    }
}
