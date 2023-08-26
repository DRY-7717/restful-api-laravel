<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{

    public function testCreateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "16515",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "16515",
                ]
            ]);
    }
    public function testCreateAddressFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "",
                "postal_code" => "16515",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => ["The country field is required."]
                ]
            ]);
    }
    public function testCreateAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            '/api/contacts/' . ($contact->id + 3) . '/addresses',
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "16515",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Address not found!"]
                ]
            ]);
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();


        $this->get(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                "Authorization" => "test"
            ],
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "16515",
                ]
            ]);
    }
    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();


        $this->get(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [
                "Authorization" => "test"
            ],
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Address not found!"]
                ]
            ]);
    }

    public function testUpdateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                "street" => "test2",
                "city" => "test2",
                "province" => "test2",
                "country" => "test2",
                "postal_code" => "16516",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "test2",
                    "city" => "test2",
                    "province" => "test2",
                    "country" => "test2",
                    "postal_code" => "16516",
                ]
            ]);
    }

    public function testUpdateAddressFailedValidation()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                "street" => "test2",
                "city" => "test2",
                "province" => "test2",
                "country" => "",
                "postal_code" => "16516",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => ["The country field is required."]
                ]
            ]);
    }
    public function testUpdateAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 2),
            [
                "street" => "test2",
                "city" => "test2",
                "province" => "test2",
                "country" => "test2",
                "postal_code" => "16516",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Address not found!"]
                ]
            ]);
    }

    public function testDeleteAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }
    public function testDeleteAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Address not found!"]
                ]
            ]);
    }

    public function testGetAllAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "street" => "test",
                        "city" => "test",
                        "province" => "test",
                        "country" => "test",
                        "postal_code" => "16515",
                    ]
                ]
            ]);
    }
    public function testGetAllAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . ($contact->id + 1) . '/addresses',
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Address not found!"]
                ]
            ]);
    }
}
