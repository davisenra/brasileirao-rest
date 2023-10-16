<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClubControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_requires_authentication(): void
    {
        $response = $this->getJson('/v1/clubs');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_it_returns_clubs(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/clubs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'state',
                ],
            ],
        ]);

        $jsonResponse = $response->decodeResponseJson();
        $this->assertNotEmpty($jsonResponse['data']);
    }

    public function test_it_returns_specific_club_with_seasons(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $club = $this->getJson('/v1/clubs');
        $clubs = $club->decodeResponseJson()['data'];
        $randomClub = array_rand($clubs);
        $clubId = $clubs[$randomClub]['id'];

        $response = $this->getJson("/v1/clubs/$clubId");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'state',
                'seasons' => [
                    '*' => [
                        'id',
                        'edition',
                        'year',
                        'firstRound',
                        'lastRound',
                    ],
                ],
            ],
        ]);
    }

    public function test_it_returns_not_found(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/clubs/999999999');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Club not found',
        ]);
    }
}
