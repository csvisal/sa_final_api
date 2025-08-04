<?php

namespace Database\Seeders;

// use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('branch')->truncate();
        DB::table('branch')->insert([
            'name' => 'FakeMart',
            'location' => 'Phnom Penh',
            'contact_number' => '099 77 49 67',
        ]);
        //
    }
}
