<?php

use App\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => config('default-user.name'),
            'email' => config('default-user.email'),
            'password' => bcrypt(config('default-user.password'))
        ]);
    }
}
