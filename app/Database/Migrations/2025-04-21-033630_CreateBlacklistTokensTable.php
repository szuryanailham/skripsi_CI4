<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlacklistTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'token'       => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'expired_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('blacklist_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('blacklist_tokens');
    }
}
