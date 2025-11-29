<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default admin credentials
        $email = 'admin@example.com';
        $name = 'Administrator';
        $password = 'Qwerty123*'; // change in production

        // Create or update admin user by email
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->role = 'admin';
        // Always ensure password is hashed; only reset if it's a new record
        if (!$user->exists) {
            $user->password = Hash::make($password);
        }
        // For safety, if somehow password not hashed (empty), set it
        if (empty($user->password)) {
            $user->password = Hash::make($password);
        }
        $user->save();
    }
}
