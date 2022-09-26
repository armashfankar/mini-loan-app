<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    
    /**
     * A test case for verfying user's registration.
     *
     * @return void
     */
    public function test_user_registration()
    {
        $response = $this->post('/api/register', [
            'user_reference_number' => 'USR12345',
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => \Hash::make('asdasdasd')
        ]);

        $response->assertStatus(200);
    }

    /**
     * A test case for verifying duplication based on email.
     *
     * @return void
     */
    public function test_user_duplication()
    {
        $user1 = User::make([
            'name' => 'Ally Bob',
            "email" => 'ally@example.com'
        ]);

        $user2 = User::make([
            'name' => 'Jason Max',
            "email" => 'jason@example.com'
        ]);

        $this->assertTrue($user1->email != $user2->email);
    }

    /**
     * A test case for verifying user exists.
     *
     * @return void
     */
    public function test_user_exists_in_database()
    {
        $this->assertDatabaseHas('users',[
            'email' => 'alex@miniloan.com'
        ]);
    }
}
