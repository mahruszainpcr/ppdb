<?php
// database/seeders/AdminUserSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['phone' => '6281234567890'],
            [
                'name' => "Admin PSB",
                'email' => 'admin@darussalam.local',
                'role' => 'admin',
                'password' => Hash::make('Admin12345!'),
            ]
        );
    }
}

