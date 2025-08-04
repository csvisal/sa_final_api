<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('position')->truncate();
        DB::table('position')->insert([
                [
                    'branch_id' => '1',
                    'name' => 'admin',
                    'description' => 'Use for admin',
                ],
                [
                    'branch_id' => '1',
                    'name' => 'sale',
                    'description' => 'Use for sale',
                ]
            ]
        );
        //
    }
}
