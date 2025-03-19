<?php

namespace App\Database\Factories;

use Faker\Factory;

class OrderFactory
{
    public static function generate($count = 1)
    {
        $faker = Factory::create();
        $orders = [];

        for ($i = 0; $i < $count; $i++) {
            $paid = $faker->boolean(); // True atau False untuk status pembayaran

            $orders[] = [
                'order_number' => strtoupper($faker->bothify('ORD-#####')),
                'user_id'      => $faker->numberBetween(1, 10), // Sesuaikan dengan jumlah user yang ada
                'event_id'     => $faker->numberBetween(1, 10), // Sesuaikan dengan jumlah event yang ada
                'total_amount' => $faker->randomFloat(2, 50, 500), // Harga antara 50 - 500
                'status'       => $faker->randomElement(['pending', 'paid', 'canceled']),
                'paid'         => $paid,
                'paid_at'      => $paid ? date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')) : null,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
        }

        return $orders;
    }
}
