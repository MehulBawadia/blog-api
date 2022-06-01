<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        $admin = User::create([
            'uuid' => Str::uuid(),
            'name' => 'User Admin',
            'email' => 'admin@example.com',
            'password' => 'Password',
        ]);
        $admin->assignRole('admin-user');

        $manager = User::create([
            'uuid' => Str::uuid(),
            'name' => 'User Manager',
            'email' => 'manager@example.com',
            'password' => 'Password',
        ]);
        $manager->assignRole('manager-user');

        $regular1 = User::create([
            'uuid' => Str::uuid(),
            'name' => 'User Regular',
            'email' => 'regular1@example.com',
            'password' => 'Password',
        ]);
        $regular1->assignRole('regular-user');

        $regular2 = User::create([
            'uuid' => Str::uuid(),
            'name' => 'User Regular',
            'email' => 'regular2@example.com',
            'password' => 'Password',
        ]);
        $regular2->assignRole('regular-user');
    }
}
