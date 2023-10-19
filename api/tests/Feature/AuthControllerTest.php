<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_can_register(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'totallysafepassword',
            'password_confirmation' => 'totallysafepassword',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'john@doe.com')->first();

        $this->assertNotNull($user);
        $this->assertNotEmpty($user->id);
        $this->assertEquals('John Doe', $user->name);
    }

    public function test_it_can_login(): void
    {
        $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'totallysafepassword',
            'password_confirmation' => 'totallysafepassword',
        ]);

        $response = $this->postJson('/access_token', [
            'email' => 'john@doe.com',
            'password' => 'totallysafepassword',
        ]);

        $response->assertStatus(200);
        $responseData = $response->decodeResponseJson();

        $this->assertSame('Successfully authenticated', $responseData['message']);
        $this->assertNotEmpty($responseData['token']);
    }
}
