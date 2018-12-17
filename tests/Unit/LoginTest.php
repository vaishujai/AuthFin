<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class LoginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = factory(\App\User::class)->create([
            'password' => bcrypt($password = '12345'),
        ]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response->assertRedirect('/home');
    }

    public function testLogin()
    {
        $user = factory(\App\User::class)->create([
            'password' => bcrypt('12345'),
        ]);
        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'random',
        ]);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }
}
