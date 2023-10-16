<?php

namespace App\Http\Resources;

use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Season
 */
class SeasonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'edition' => $this->edition_name,
            'year' => $this->year,
            'firstRound' => $this->first_round,
            'lastRound' => $this->last_round,
            'roundsCount' => $this->whenCounted('rounds'),
            'clubs' => ClubResource::collection($this->whenLoaded('clubs')),
            'rounds' => RoundResource::collection($this->whenLoaded('rounds')),
        ];
    }
}
