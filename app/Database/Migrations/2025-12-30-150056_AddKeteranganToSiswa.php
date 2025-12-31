<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganToSiswa extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('keterangan', 'tb_siswa')) {
            $this->forge->addColumn('tb_siswa', [
                'keterangan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'no_hp'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('keterangan', 'tb_siswa')) {
            $this->forge->dropColumn('tb_siswa', 'keterangan');
        }
    }
}
