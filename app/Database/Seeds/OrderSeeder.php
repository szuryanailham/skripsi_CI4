<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Database\Factories\OrderFactory;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $orders = OrderFactory::generate(10); // Generate 10 pesanan dummy

        $this->db->table('orders')->insertBatch($orders);
    }
}
