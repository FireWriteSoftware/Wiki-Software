<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            [
                'name' => 'User',
                'color_code' => '242424',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Moderator',
                'color_code' => '0d8028',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Administrator',
                'color_code' => '800d0d',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
