<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SeasonService;
use App\Http\Resources\SeasonResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonController extends Controller
{
    public function __construct(
        private readonly SeasonService $seasonService
    ) {
    }

    public function index(): JsonResource
    {
        $seasons = $this->seasonService->all();

        return SeasonResource::collection($seasons);
    }

    public function show(int $id): JsonResource
    {
        $season = $this->seasonService->find($id);

        if ($season === null) {
            abort(404, 'Season not found');
        }

        return new SeasonResource($season);
    }
}
