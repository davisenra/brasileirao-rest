<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StadiumControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_requires_authentication(): void
    {
        $response = $this->getJson('/v1/stadiums');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_it_returns_stadiums(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/stadiums');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'roundsCount',
                ],
            ],
        ]);

        $jsonResponse = $response->decodeResponseJson();
        $this->assertNotEmpty($jsonResponse['data']);
    }

    public function test_it_returns_error_when_stadium_not_found(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/v1/stadiums/9999999');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Stadium not found',
        ]);
    }

    public function test_it_returns_all_rounds_from_stadium(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $stadium = $this->getJson('/v1/stadiums');
        $stadiums = $stadium->decodeResponseJson()['data'];
        $randomStadium = array_rand($stadiums);
        $stadiumId = $stadiums[$randomStadium]['id'];

        $response = $this->getJson("/v1/stadiums/$stadiumId");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
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
                        'season' => [
                            'id',
                            'edition',
                            'year',
                            'firstRound',
                            'lastRound',
                        ],
                    ],
                ],
            ],
        ]);

        $jsonResponse = $response->decodeResponseJson();
        $this->assertNotEmpty($jsonResponse['data']['rounds']);
    }
}
