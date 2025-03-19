<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Database\Factories\EventFactory;

class EventSeeder extends Seeder
{
    public function run()
    {
        $events = EventFactory::generate(10); // Generate 10 event dummy

        $this->db->table('events')->insertBatch($events);
    }
}
