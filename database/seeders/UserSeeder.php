<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('users')->truncate();
        DB::table('users')->insert([
                [
                    'name' => 'admin',
                    'email' => 'admin@mail.com',
                    'password' =>  Hash::make('456'),
                    'staff_id' => 1,
                ],
                [
                    'name' => 'sale',
                    'email' => 'sale@mail.com',
                    'password' =>  Hash::make('123'),
                    'staff_id' => 2,
                ]
            ]
        );
        //
    }
}
