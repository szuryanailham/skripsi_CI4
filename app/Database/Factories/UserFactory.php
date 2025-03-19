<?php

namespace App\Database\Factories;

use Faker\Factory;

class UserFactory
{
    public static function generate($count = 1)
    {
        $faker = Factory::create();
        $users = [];

        for ($i = 0; $i < $count; $i++) {
            $users[] = [
                'name'      => $faker->name,
                'email'     => $faker->unique()->safeEmail,
                'password'  => password_hash('password123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        return $users;
    }
}
