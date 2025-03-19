<?php

namespace App\Database\Factories;

use Faker\Factory;

class EventFactory
{
    public static function generate($count = 1)
    {
        $faker = Factory::create();
        $events = [];

        for ($i = 0; $i < $count; $i++) {
            $title = $faker->sentence(3);
            $events[] = [
                'title'       => $title,
                'slug'        => strtolower(str_replace(' ', '-', $title)),
                'description' => $faker->paragraph(),
                'price'       => $faker->randomFloat(2, 0, 1000),
                'location'    => $faker->address,
                'date'        => $faker->date(),
                'time'        => $faker->time(),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }

        return $events;
    }
}
