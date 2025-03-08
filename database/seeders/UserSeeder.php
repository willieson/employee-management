<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //// Buat 50 user dummy
        User::factory()->count(10)->create()->each(function ($user) {
            // Set id_superior secara acak ke user lain (kecuali diri sendiri)
            $superior = User::where('id', '!=', $user->id)->inRandomOrder()->first();
            $user->id_superior = $superior ? $superior->id : null;
            $user->save();
        });
    }
}
