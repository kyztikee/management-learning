<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateLurahAccount extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Lurah default',
            'email' => 'lurah@bengkong.com',
            'password' => Hash::make('admin'),
            'role' => 1
        ]);
    }
}
