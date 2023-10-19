<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Season;
use App\Services\SeasonService;
use App\Http\Resources\SeasonResource;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Authenticated;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

class SeasonController extends Controller
{
    public function __construct(
        private readonly SeasonService $seasonService
    ) {
    }

    #[Authenticated]
    #[ResponseFromApiResource(
        SeasonResource::class,
        Season::class,
        collection: true,
        with: ['clubs']
    )]
    public function index(): JsonResource
    {
        $seasons = $this->seasonService->all();

        return SeasonResource::collection($seasons);
    }

    #[Authenticated]
    #[ResponseFromApiResource(
        SeasonResource::class,
        Season::class,
        collection: true,
        with: [
            'clubs',
            'rounds',
            'rounds.awayClub',
            'rounds.homeClub',
            'rounds.stadium'
        ]
    )]
    #[Response([
        'message' => 'Season not found',
    ], 404)]
    public function show(int $id): JsonResource
    {
        $season = $this->seasonService->find($id);

        if ($season === null) {
            abort(404, 'Season not found');
        }

        return new SeasonResource($season);
    }
}
