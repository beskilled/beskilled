<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class RegisterControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user = [
        'first_name' => 'Kenzo',
        'last_name' => 'Hayashi',
        'email' => "hanzo@ninja.co",
        'password' => 'secret',
        'user_type' => 'student',
        'username' => '',
    ];

    /** @test */
    public function it_should_not_be_able_to_register_with_incomplete_params()
    {
        $this->json('POST', '/register', [])
             ->seeJsonStructure(['errors'])
             ->seeJsonContains(['password' => ["The password field is required."]])
             ->seeJsonContains(['email' => ["The email field is required."]]);
    }

    /** @test */
    public function it_should_not_be_able_to_register_with_invalid_email_address()
    {
        $userEmailError = $this->user;
        $userEmailError['email'] = 'hello';
        $this->json('POST', '/register', $userEmailError)
             ->seeJsonContains(['email' => ["The email must be a valid email address."]])
             ->seeJsonStructure(['errors']);
    }

    /** @test */
    public function it_should_not_be_able_to_regiser_with_invalid_user_type()
    {
        $userTypeError = $this->user;
        $userTypeError['user_type'] = 'librarian';
        $this->json('POST', '/register', $userTypeError)
             ->seeJsonContains(["user_type" => ["The selected user type is invalid."]])
             ->seeJsonStructure(['errors']);
    }

    /** @test */
    public function it_should_return_duplicate_entry()
    {
        $this->expectsEvents(['Illuminate\Auth\Events\Registered']);
        $this->json('POST', '/register', $this->user);
        $this->json('POST', '/register', $this->user)
             ->seeJsonContains(['error' => 'Duplicate entry'])
             ->assertResponseStatus(400);
    }

    /** @test */
    public function it_should_be_able_to_register_with_complete_param()
    {
        $this->expectsEvents(['Illuminate\Auth\Events\Registered']);
        $this->json('POST', '/register', $this->user)
            ->seeJsonStructure(['token'])
            ->assertResponseStatus(200);
    }
}
