<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentOnetoOneTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_member_onetoone' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'status_invoice' => [
                'type'       => 'ENUM',
                'constraint' => ['paid', 'unpaid'],
                'default'    => 'unpaid',
            ],
            'link_invoice' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'invoice_date' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary Key
        $this->forge->addForeignKey('id_member_onetoone', 'tb_member_onetone', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('tb_payment_onetoone');
    }

    public function down()
    {
        $this->forge->dropTable('tb_payment_onetoone', true);
    }
}
