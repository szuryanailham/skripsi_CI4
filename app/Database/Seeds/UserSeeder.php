<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Database\Factories\UserFactory;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = UserFactory::generate(10); // Generate 10 user dummy

        $this->db->table('users')->insertBatch($users);
    }
}
