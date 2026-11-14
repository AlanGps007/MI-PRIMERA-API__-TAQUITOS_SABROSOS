<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaquitoRequest;
use App\Http\Requests\UpdateTaquitoRequest;
use App\Http\Resources\TaquitoResource;
use App\Models\Taquito;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaquitoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $taquitos = Taquito::query()
            ->orderBy('id')
            ->paginate(10);

        return TaquitoResource::collection($taquitos);
    }

    public function store(StoreTaquitoRequest $request): JsonResponse
    {
        $taquito = Taquito::create($request->validated());

        return (new TaquitoResource($taquito))
            ->additional(['message' => 'Taquito creado correctamente.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Taquito $taquito): TaquitoResource
    {
        return new TaquitoResource($taquito);
    }

    public function update(UpdateTaquitoRequest $request, Taquito $taquito): TaquitoResource
    {
        $taquito->update($request->validated());

        return (new TaquitoResource($taquito))
            ->additional(['message' => 'Taquito actualizado correctamente.']);
    }

    public function destroy(Taquito $taquito): JsonResponse
    {
        $taquito->delete();

        return response()->json([
            'message' => 'Taquito eliminado correctamente.',
        ]);
    }
}
