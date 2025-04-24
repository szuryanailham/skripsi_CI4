<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToUsers extends Migration
{
    public function up()
    {
        // Menambah kolom 'status' pada tabel 'users'
        $this->forge->addColumn('users', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['user', 'admin'], 
                'default'    => 'user',
            ],
        ]);
    }

    public function down()
    {
        // Menghapus kolom 'status' dari tabel 'users'
        $this->forge->dropColumn('users', 'status');
    }
}
