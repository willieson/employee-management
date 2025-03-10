<?php

namespace Database\Seeders;

use App\Models\Annual_leaves;
use App\Models\expired_leaves;
use App\Models\Leave_types;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'name' => 'Administrator',
            'address' => 'This-Address',
            'contact' => 'This-contact',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin'),
            'role' => 'HRD',

        ]);

        Leave_types::create([
            'id' => 1,
            'name' => 'Annual',
            'reduces_annual_leave' => true,

        ]);

        Leave_types::create([
            'id' => 2,
            'name' => 'Sick',
            'reduces_annual_leave' => false,
        ]);

        expired_leaves::create([
            'year' => 2023,
            'expires_at' => Carbon::parse('2024-08-15'),
        ]);

        expired_leaves::create([
            'year' => 2024,
            'expires_at' => Carbon::parse('2025-08-15'),
        ]);

        expired_leaves::create([
            'year' => 2025,
            'expires_at' => Carbon::parse('2026-08-15'),
        ]);

        Annual_leaves::create([
            'users_id' => 1,
            'year' => 2024,
            'total_leave' => '18',
            'remaining_leave' => '18',
        ]);
        Annual_leaves::create([
            'users_id' => 1,
            'year' => 2025,
            'total_leave' => '18',
            'remaining_leave' => '18',
        ]);
    }
}
