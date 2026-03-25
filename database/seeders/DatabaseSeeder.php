<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'test@test.ee'],
            [
                'name' => 'Test User',
                'password' => Hash::make('test@test.ee'),
                'is_admin' => true,
            ],
        );

        $this->call([
            AuthorSeeder::class,
            PostSeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
