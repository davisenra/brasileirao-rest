<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SeasonControllerTest extends TestCase
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

    public function test_it_returns_all_seasons(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/seasons');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'edition',
                    'year',
                    'firstRound',
                    'lastRound',
                    'clubs' => [
                        '*' => [
                            'id',
                            'name',
                            'state',
                        ],
                    ],
                ],
            ],
        ]);

        $jsonResponse = $response->decodeResponseJson();
        $this->assertNotEmpty($jsonResponse['data']);
    }

    public function test_seasons_returned_are_ordered_by_year(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/seasons');

        $response->assertStatus(200);
        $jsonResponse = $response->decodeResponseJson();

        $seasons = $jsonResponse['data'];
        $this->assertNotEmpty($seasons);

        $lastYear = 0;

        foreach ($seasons as $season) {
            $this->assertGreaterThanOrEqual($lastYear, $season['year']);
            $lastYear = $season['year'];
        }
    }

    public function test_it_returns_all_clubs_participating_in_the_season(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/seasons');
        $seasons = $response->decodeResponseJson()['data'];

        foreach ($seasons as $season) {
            $this->assertNotEmpty($season['clubs']);
        }
    }

    public function test_it_returns_specific_season(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/seasons');
        $seasons = $response->decodeResponseJson()['data'];
        $randomSeason = array_rand($seasons);
        $seasonId = $seasons[$randomSeason]['id'];

        $response = $this->getJson("/v1/seasons/$seasonId");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'edition',
                'year',
                'firstRound',
                'lastRound',
                'roundsCount',
                'clubs' => [
                    '*' => [
                        'id',
                        'name',
                        'state',
                    ],
                ],
                'rounds' => [
                    '*' => [
                        'id',
                        'date',
                        'result',
                        'roundNumber',
                        'homeClub' => [
                            'id',
                            'score',
                        ],
                        'awayClub' => [
                            'id',
                            'score',
                        ],
                        'stadium' => [
                            'id',
                            'name',
                        ],
                    ],
                ]
            ],
        ]);
    }
}
