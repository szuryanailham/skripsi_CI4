<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProofImageToOrders extends Migration
{
    public function up()
{
    $this->forge->addColumn('orders', [
        'proof_image' => [
            'type'       => 'VARCHAR',
            'constraint' => '255',
            'null'       => true,
            'after'      => 'id'
        ],
    ]);
}

   public function down()
{
    $this->forge->dropColumn('orders', 'proof_image');
}
}
