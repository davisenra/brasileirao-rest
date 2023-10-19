<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Stadium;
use App\Services\StadiumService;
use App\Http\Resources\StadiumResource;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Authenticated;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

class StadiumController extends Controller
{
    public function __construct(
        private readonly StadiumService $stadiumService
    ) {
    }

    #[Authenticated]
    #[ResponseFromApiResource(
        StadiumResource::class,
        Stadium::class,
        collection: true,
    )]
    public function index(): JsonResource
    {
        $stadiums = $this->stadiumService->all();

        return StadiumResource::collection($stadiums);
    }

    #[Authenticated]
    #[ResponseFromApiResource(
        StadiumResource::class,
        Stadium::class,
        collection: true,
        with: [
            'rounds',
            'rounds.season',
            'rounds.awayClub',
            'rounds.homeClub'
        ]
    )]
    #[Response([
        'message' => 'Stadium not found',
    ], 404)]
    public function show(int $id): JsonResource
    {
        $stadium = $this->stadiumService->find($id);

        if ($stadium === null) {
            abort(404, 'Stadium not found');
        }

        return new StadiumResource($stadium);
    }
}
