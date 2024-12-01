<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = new User();
        $user->avatar = "tested";
        $user->name = "John Doe";
        $user->email = "john@example.com";
        $user->password = Hash::make('your_password'); // Hashing the password using Bcrypt
        $user->save();
    }
}
