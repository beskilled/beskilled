<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Carbon\Carbon;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user = [
        'email' => 'hanzo@ninja.co',
        'password' => 'secret'
    ];

    /** @test */
    public function it_can_not_login_if_the_parameters_are_incomplete()
    {
        factory(\App\User::class)->create(['email' => $this->user['email']]);

        $this->json('POST', '/login', [])
            ->seeJsonStructure(['errors'])
            ->seeJsonContains(['password' => ["The password field is required."]])
            ->seeJsonContains(['email' => ["The email field is required."]]);
    }

    /** @test */
    public function it_should_not_be_able_to_login_if_the_credentials_are_invalid()
    {
        $this->json('POST', '/login', $this->user)
             ->seeJsonContains(['error' => 'Unauthorized'])
             ->assertResponseStatus(401);
    }

    /** @test */
    public function it_should_receive_token_invalid_exception()
    {
        $this->json('GET', '/me', ['Authorization' => 'Bearer abcdefg'])
            ->seeJsonContains(['errors' => 'Token not provided']);
    }

    /** @test */
    public function it_should_be_able_to_login()
    {
        factory(\App\User::class)->create(['email' => $this->user['email']]);

        $this->json('POST', '/login', $this->user)
            ->seeJsonStructure(['access_token']);
    }

}
