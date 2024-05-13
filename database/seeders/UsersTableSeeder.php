<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::create([
        //     'username' => 'annisan',
        //     'email' => 'annisaa@gmail.com',
        //     'password' => Hash::make('admin),
        //     'role' => 'admin',
        
        // ]);

        User::create([
            'username' => 'Annisa',
            'email' => 'annisa@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        
        ]);
    }
}