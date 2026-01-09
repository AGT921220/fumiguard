<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\ServicePlans\CreateServicePlanUseCase;
use App\Application\UseCases\ServicePlans\DeleteServicePlanUseCase;
use App\Application\UseCases\ServicePlans\GetServicePlanUseCase;
use App\Application\UseCases\ServicePlans\ListServicePlansUseCase;
use App\Application\UseCases\ServicePlans\UpdateServicePlanUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServicePlanController extends Controller
{
    public function index(ListServicePlansUseCase $useCase)
    {
        return response()->json($useCase->execute());
    }

    public function show(int $id, GetServicePlanUseCase $useCase)
    {
        return response()->json($useCase->execute($id));
    }

    public function store(Request $request, CreateServicePlanUseCase $useCase)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'checklist_template' => ['nullable', 'array'],
            'certificate_template' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return response()->json($useCase->execute($data), 201);
    }

    public function update(int $id, Request $request, UpdateServicePlanUseCase $useCase)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'checklist_template' => ['sometimes', 'nullable', 'array'],
            'certificate_template' => ['sometimes', 'nullable', 'array'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
        ]);

        return response()->json($useCase->execute($id, $data));
    }

    public function destroy(int $id, DeleteServicePlanUseCase $useCase)
    {
        $useCase->execute($id);

        return response()->json(['ok' => true]);
    }
}
