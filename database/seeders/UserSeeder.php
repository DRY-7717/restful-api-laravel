<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            "username" => "bima",
            "name" => "bima",
            "password" => bcrypt("password"),
            "token" => "test"
        ]);
        User::create([
            "username" => "bima2",
            "name" => "bim2",
            "password" => bcrypt("password"),
            "token" => "test2"
        ]);
    }
}
