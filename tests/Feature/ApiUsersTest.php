<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiUsersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use WithFaker;

    public function test_register_error()
    {
        $response = $this->post('/api/register',[]);
        $response->assertStatus(422);
    }

    public function test_register()
    {
        $response = $this->post('/api/register',
        [
            'name'=>$this->faker->firstName,
            'email'=>$this->faker->unique()->email,
            'password'=>'123456',
            'password_confirmation'=>'123456'
        ]);
        $response->assertStatus(200)->assertJsonCount(3)->assertJsonStructure(
            [
                'status',
                'user'=>[
                    'id',
                    'name',
                    'email'
                ],
                'authorisation'=>[
                    'token',
                    'type'
                ]
            ]
        );
    }

    public function test_login_bad_cred()
    {
        $user = User::create([
            'name'=>$this->faker->firstName,
            'email'=>$this->faker->unique()->email,
            'password'=>bcrypt('123456'),
        ]);
        $response = $this->post('/api/login',
            [
                'email'=>$user->email,
                'password'=>'12345',
            ]);
        $response->assertStatus(401)->assertJsonCount(1)->assertJson([
            'message'=>'Bad credentials'
        ]);
    }

    public function test_login()
    {
        $user = User::create([
            'name'=>$this->faker->firstName,
            'email'=>$this->faker->unique()->email,
            'password'=>bcrypt('123456'),
        ]);

        $response = $this->post('/api/login',
            [
                'email'=>$user->email,
                'password'=>'123456',
            ]);
        $response->assertStatus(200)->assertJsonCount(3)->assertJsonStructure(
                [
                    'status',
                    'user'=>[
                        'id',
                        'name',
                        'email'
                    ],
                    'authorisation'=>[
                        'token',
                        'type'
                    ]
                ]
        );
    }
}
