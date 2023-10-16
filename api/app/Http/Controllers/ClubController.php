<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ClubService;
use App\Http\Resources\ClubResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubController extends Controller
{
    public function __construct(
        private readonly ClubService $clubService
    ) {
    }

    public function index(): JsonResource
    {
        $clubs = $this->clubService->all();

        return ClubResource::collection($clubs);
    }

    public function show(int $id): JsonResource
    {
        $club = $this->clubService->find($id);

        if ($club === null) {
            abort(404, 'Club not found');
        }

        return new ClubResource($club);
    }
}
