<?php

namespace App\Http\Resources;

use App\Models\Round;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Round
 */
class RoundResource extends JsonResource
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
            'date' => $this->date,
            'result' => $this->result->name,
            'roundNumber' => $this->round_number,
            'homeClub' => $this->whenLoaded('homeClub', function () {
                return [
                    'id' => $this->home_club_id,
                    'score' => $this->home_club_score,
                ];
            }),
            'awayClub' => $this->whenLoaded('awayClub', function () {
                return [
                    'id' => $this->away_club_id,
                    'score' => $this->away_club_score,
                ];
            }),
            'stadium' => $this->whenLoaded('stadium'),
            'season' => SeasonResource::make($this->whenLoaded('season')),
        ];
    }
}
