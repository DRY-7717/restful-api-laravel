<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateContactSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contacts',
            [
                "first_name" => "Bima",
                "last_name" => "Arya Wicaksana",
                "email" => "wicaksanabimaarya@gmail.com",
                "phone" => "089638307725",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "Bima",
                    "last_name" => "Arya Wicaksana",
                    "email" => "wicaksanabimaarya@gmail.com",
                    "phone" => "089638307725",
                ]
            ]);
    }
    public function testCreateContactFailed()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contacts',
            [
                "first_name" => "",
                "last_name" => "Arya Wicaksana",
                "email" => "wicaksanabimaarya@gmail.com",
                "phone" => "089638307725",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }
    public function testCreateContactUnauthorize()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contacts',
            [
                "first_name" => "bima",
                "last_name" => "bima",
                "email" => "bima@gmail.com",
                "phone" => "089638307725",
            ],
            [
                "Authorization" => "test3"
            ]
        )->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id,  [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "bima",
                    "last_name" => "bima",
                    "email" => "bima@gmail.com",
                    "phone" => "089638307725"
                ]
            ]);
    }
    public function testGetContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1),  [
            "Authorization" => "test"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Contact not found!"
                    ]
                ]
            ]);
    }
    public function testGetContactOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id,  [
            "Authorization" => "test2"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Contact not found!"
                    ]
                ]
            ]);
    }

    public function testUpdateContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $contact->id,
            [
                "first_name" => "bima2",
                "last_name" => "bima",
                "email" => "bima@gmail.com",
                "phone" => "089638307725"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "bima2",
                    "last_name" => "bima",
                    "email" => "bima@gmail.com",
                    "phone" => "089638307725"
                ]
            ]);
    }
    public function testUpdateContactValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $contact->id,
            [
                "first_name" => "",
                "last_name" => "bima",
                "email" => "bima@gmail.com",
                "phone" => "089638307725"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }
    public function testUpdateContactOtherUser()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $contact->id,
            [
                "first_name" => "bima",
                "last_name" => "bima",
                "email" => "bima@gmail.com",
                "phone" => "089638307725"
            ],
            [
                "Authorization" => "test2"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Contact not found!"
                    ]
                ]
            ]);
    }

    public function testDeleteContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $contact->id,
            [],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }
    public function testDeleteContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . ($contact->id + 1),
            [],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Contact not found!"]
                ]
            ]);
    }

    public function testSearchContactByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get(
            '/api/contacts?name=first',
            [
                "Authorization" => 'test'
            ]
        )
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchContactByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get(
            '/api/contacts?name=last',
            [
                "Authorization" => 'test'
            ]
        )
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchContactByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get(
            '/api/contacts?email=test',
            [
                "Authorization" => 'test'
            ]
        )
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchContactByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get(
            '/api/contacts?phone=11111',
            [
                "Authorization" => 'test'
            ]
        )
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchContactNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get(
            '/api/contacts?name=tidakada',
            [
                "Authorization" => 'test'
            ]
        )
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }
    public function testSearchContactWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get(
            '/api/contacts?size=5&page=2',
            [
                "Authorization" => 'test'
            ]
        )
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
