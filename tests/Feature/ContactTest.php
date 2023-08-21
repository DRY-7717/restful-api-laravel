<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
