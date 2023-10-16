<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\StadiumService;
use App\Http\Resources\StadiumResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StadiumController extends Controller
{
    public function __construct(
        private readonly StadiumService $stadiumService
    ) {
    }

    public function index(): JsonResource
    {
        $stadiums = $this->stadiumService->all();

        return StadiumResource::collection($stadiums);
    }

    public function show(int $id): JsonResource
    {
        $stadium = $this->stadiumService->find($id);

        if ($stadium === null) {
            abort(404, 'Stadium not found');
        }

        return new StadiumResource($stadium);
    }
}
