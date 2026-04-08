<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Badge;
use App\Models\BloodRequest;
use App\Models\Comment;
use App\Models\Donation;
use App\Models\Point;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Comment::query()->truncate();
        Donation::query()->truncate();
        Point::query()->truncate();
        BloodRequest::query()->truncate();
        DB::table('badge_user')->truncate();
        Badge::query()->truncate();
        User::query()->truncate();
        DB::table('notifications')->truncate();
        DB::table('personal_access_tokens')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Badge::query()->insert([
            [
                'key' => 'life-saver',
                'name' => 'Life Saver',
                'description' => 'Awarded after the first completed donation.',
                'threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'active-donor',
                'name' => 'Active Donor',
                'description' => 'Awarded after three completed donations.',
                'threshold' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        User::query()->create([
            'name' => 'Emine',
            'email' => 'emine@ab.com',
            'phone' => '0600000001',
            'city' => 'Nouakchott',
            'role' => UserRole::Superadmin,
            'preferred_locale' => 'ar',
            'is_guest' => false,
            'is_suspended' => false,
            'posting_restricted' => false,
            'profile_locked' => false,
            'password' => Hash::make('ihya@2o2six'),
        ]);
    }
}
