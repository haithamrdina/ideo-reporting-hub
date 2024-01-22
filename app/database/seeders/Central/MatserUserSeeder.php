<?php

namespace Database\Seeders\Central;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MatserUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'lastname' => 'Master',
            'firstname' => 'Master',
            'email' => 'master@ideo-learn.com',
            'password' => Hash::make('password'),
        ]);
    }
}
