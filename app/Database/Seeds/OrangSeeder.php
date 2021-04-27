<?php

namespace App\Database\Seeds;

use CodeIgniter\I18n\Time;

class OrangSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        // $data = [
        //     [
        //         'nama'          => 'Yustian Dikma',
        //         'alamat'        => 'Banyubiru',
        //         'created_at'    => Time::now(),
        //         'updated_at'    => Time::now()
        //     ],
        //     [
        //         'nama'          => 'Helisma Putri',
        //         'alamat'        => 'Bandung',
        //         'created_at'    => Time::now(),
        //         'updated_at'    => Time::now()
        //     ],
        //     [
        //         'nama'          => 'Indah Cahya',
        //         'alamat'        => 'Padang',
        //         'created_at'    => Time::now(),
        //         'updated_at'    => Time::now()
        //     ]
        // ];

        $faker = \Faker\Factory::create('id_ID');
        for ($i = 0; $i < 100; $i++) {
            # code...
            $data = [
                'nama'          => $faker->name,
                'alamat'        => $faker->address,
                'created_at'    => Time::createFromTimestamp($faker->unixTime()),
                'updated_at'    => Time::now()
            ];

            // Using Query Builder
            $this->db->table('orang')->insert($data);
        }


        // Simple Queries
        // $this->db->query(
        //     "INSERT INTO orang (nama, alamat, created_at, updated_at) 
        //         VALUES(:nama:, :alamat: , :created_at:, :updated_at:)",
        //     $data
        // );

        // Using Query Builder For Batch Data
        // $this->db->table('orang')->insertBatch($data);
    }
}
