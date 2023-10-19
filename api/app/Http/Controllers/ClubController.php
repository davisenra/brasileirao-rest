<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Club;
use App\Services\ClubService;
use App\Http\Resources\ClubResource;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Authenticated;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

class ClubController extends Controller
{
    public function __construct(
        private readonly ClubService $clubService
    ) {
    }

    #[Authenticated]
    #[ResponseFromApiResource(
        ClubResource::class,
        Club::class,
        collection: true
    )]
    public function index(): JsonResource
    {
        $clubs = $this->clubService->all();

        return ClubResource::collection($clubs);
    }

    #[Authenticated]
    #[ResponseFromApiResource(
        ClubResource::class,
        Club::class,
        with: ['seasons']
    )]
    #[Response([
        'message' => 'Club not found',
    ], 404)]
    public function show(int $id): JsonResource
    {
        $club = $this->clubService->find($id);

        if ($club === null) {
            abort(404, 'Club not found');
        }

        return new ClubResource($club);
    }
}
