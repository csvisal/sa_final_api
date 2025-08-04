<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('staff')->truncate();
        DB::table('staff')->insert([
            [
                'position_id' => '1',
                'name' => 'admin',
                'gender' => 'male',
                'dob' => '2001-01-01',
                'pob' => 'Phnom Penh',
                'address' => 'Phnom Penh',
                'phone' => '2222',
                'national_id_card' => 'khmer',
            ],
            [
                'position_id' => '2',
                'name' => 'sale',
                'gender' => 'female',
                'dob' => '2025-02-02',
                'pob' => 'Takeo',
                'address' => 'Takeo',
                'phone' => '4444',
                'national_id_card' => 'french',
            ],
        ]);
        //
    }
}
