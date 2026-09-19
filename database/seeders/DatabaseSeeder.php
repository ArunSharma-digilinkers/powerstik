<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Creates the first admin from ADMIN_EMAIL / ADMIN_PASSWORD. Safe to re-run:
     * an existing account is left untouched.
     */
    public function run(): void
    {
        $email = config('powerstik.admin_email');
        $password = config('powerstik.admin_password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD not set; no admin created.');

            return;
        }

        User::firstOrCreate(['email' => $email], [
            'name' => 'Administrator',
            'password' => $password,
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
