<?php

namespace App\Http\Resources;

use App\Models\Stadium;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Stadium
 */
class StadiumResource extends JsonResource
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
            'name' => $this->name,
            'roundsCount' => $this->whenCounted('rounds'),
            'rounds' => RoundResource::collection($this->whenLoaded('rounds')),
        ];
    }
}
