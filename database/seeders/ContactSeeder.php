<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user = User::where("username", "bima")->first();
        Contact::create([
            "first_name" => "bima",
            "last_name" => "bima",
            "email" => "bima@gmail.com",
            "phone" => "089638307725",
            "user_id" => $user->id
        ]);
    }
}
