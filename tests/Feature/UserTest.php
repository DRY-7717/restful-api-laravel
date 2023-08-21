<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotEquals;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            "username" => "bima",
            "name" => "bima",
            "password" => "adsfsadf"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "bima",
                    "name" => "bima",
                ]
            ]);
    }
    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            "username" => "",
            "name" => "",
            "password" => ""
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                ]
            ]);
    }
    public function testRegisterUsernameAlreadtExist()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            "username" => "bima",
            "name" => "bima",
            "password" => "adsfsadf"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already exists."
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            "username" => "bima",
            "password" => "password"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "bima",
                    "name" => "bima",
                ]
            ]);

        $user = User::where("username", "bima")->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post('/api/users/login', [
            "username" => "bimaarya1223",
            "password" => "password"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ],
                ]
            ]);
    }
    public function testLoginFailedPasswordWrong()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            "username" => "bima",
            "password" => "asdfasdf"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ],
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/users/current", [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "bima",
                    "name" => "bima"
                ]
            ]);
    }
    public function testGetUnauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/users/current")->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }
    public function testGetInvalidToken()
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/users/current", [
            "Authorization" => "asdf"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser =  User::where('username', 'bima')->first();
        $this->patch(
            "/api/users/current",
            [
                "password" => "new"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "bima",
                    "name" => "bima"
                ]
            ]);
        $newUser =  User::where('username', 'bima')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }
    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser =  User::where('username', 'bima')->first();
        $this->patch(
            "/api/users/current",
            [
                "name" => "bima2"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "bima",
                    "name" => "bima2"
                ]
            ]);
        $newUser =  User::where('username', 'bima')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }
    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->patch(
            "/api/users/current",
            [
                "name" => "BimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBimaBima"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field must not be greater than 100 characters."]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->delete(uri: '/api/users/logout', headers: [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
        $user = User::where('username', "bima")->first();
        self::assertNull($user->token);
    }
    public function testLogoutFailed()
    {
        $this->seed(UserSeeder::class);
        $this->delete(uri: '/api/users/logout', headers: [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Unauthorized"]
                ]
            ]);
    }
}
