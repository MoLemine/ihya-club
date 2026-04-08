<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BloodDonationPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Badge::query()->create([
            'key' => 'life-saver',
            'name' => 'Life Saver',
            'description' => 'First completed donation',
            'threshold' => 1,
        ]);

        Badge::query()->create([
            'key' => 'active-donor',
            'name' => 'Active Donor',
            'description' => 'Three completed donations',
            'threshold' => 3,
        ]);
    }

    public function test_guest_can_submit_blood_request(): void
    {
        $response = $this->post(route('requests.store'), [
            'name' => 'Guest User',
            'phone' => '0600999988',
            'city' => 'Nouakchott',
            'hospital_name' => 'CHN',
            'urgency_level' => 'urgent',
            'required_units' => 2,
            'description' => 'Urgent O- request',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'phone' => '0600999988',
            'is_guest' => true,
        ]);

        $this->assertDatabaseHas('blood_requests', [
            'hospital_name' => 'CHN',
            'status' => 'pending',
            'blood_type' => 'O-',
        ]);
    }

    public function test_completed_donation_auto_completes_request_and_awards_points(): void
    {
        $user = User::factory()->create([
            'last_donation_date' => now()->subMonths(4)->toDateString(),
        ]);

        $bloodRequest = BloodRequest::factory()->create([
            'user_id' => $user->id,
            'required_units' => 1,
            'status' => 'approved',
        ]);

        $this->post(route('donations.store', $bloodRequest), [
            'name' => $user->name,
            'phone' => $user->phone,
            'city' => $user->city,
        ]);

        $donation = $user->donations()->firstOrFail();

        $this->patch(route('donations.complete', $donation))
            ->assertRedirect();

        $this->assertDatabaseHas('blood_requests', [
            'id' => $bloodRequest->id,
            'status' => 'completed',
            'fulfilled_units' => 1,
        ]);

        $this->assertDatabaseHas('points', [
            'user_id' => $user->id,
            'action' => 'donation',
            'value' => 10,
        ]);
    }

    public function test_public_profile_is_visible_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'profile_locked' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => 'member@example.com',
            'password' => 'password',
        ])->assertRedirect(route('profile.me'));

        $this->get(route('profiles.show', $user))
            ->assertOk()
            ->assertSee($user->name);
    }
}
