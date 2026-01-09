<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Sites\CreateSiteUseCase;
use App\Application\UseCases\Sites\DeleteSiteUseCase;
use App\Application\UseCases\Sites\GetSiteUseCase;
use App\Application\UseCases\Sites\ListSitesByCustomerUseCase;
use App\Application\UseCases\Sites\UpdateSiteUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function indexByCustomer(int $customerId, ListSitesByCustomerUseCase $useCase)
    {
        return response()->json($useCase->execute($customerId));
    }

    public function show(int $id, GetSiteUseCase $useCase)
    {
        return response()->json($useCase->execute($id));
    }

    public function store(Request $request, CreateSiteUseCase $useCase)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        return response()->json($useCase->execute($data), 201);
    }

    public function update(int $id, Request $request, UpdateSiteUseCase $useCase)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'address_line1' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_line2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country' => ['sometimes', 'nullable', 'string', 'max:255'],
            'lat' => ['sometimes', 'nullable', 'numeric'],
            'lng' => ['sometimes', 'nullable', 'numeric'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        return response()->json($useCase->execute($id, $data));
    }

    public function destroy(int $id, DeleteSiteUseCase $useCase)
    {
        $useCase->execute($id);

        return response()->json(['ok' => true]);
    }
}
