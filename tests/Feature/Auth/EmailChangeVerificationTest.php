<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

beforeEach(function () {
    // Seed the database with roles, permissions, and users
    $this->seed(ShieldSeeder::class);
    $this->seed(RoleUserSeeder::class);

    // Get all test users created by the seeders
    $this->superAdmin = User::where('email', 'superadmin@filamentum.com')->first();
    $this->admin = User::where('email', 'admin@filamentum.com')->first();
    $this->regularUser = User::where('email', 'user@filamentum.com')->first();
});

// ------------------------------------------------------------------------------------------------
// Email Change Verification Process Tests
// ------------------------------------------------------------------------------------------------

it('allows successful email change verification process with valid data', function () {
    // Create a user
    $user = User::factory()->create([
        'email' => 'old@example.com',
    ]);

    // Assign user role
    $user->assignRole('User');

    // Authenticate the user
    Livewire::actingAs($user);

    // Generate email change verification URL with valid signature
    $verificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-change-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'email' => encrypt('new@example.com'),
        ]
    );

    // Extract signature from URL for caching
    $parsedUrl = parse_url($verificationUrl);
    parse_str($parsedUrl['query'], $queryParams);
    $signature = $queryParams['signature'];

    // Store signature in cache as required by EmailChangeVerificationRequest
    cache()->put($signature, true, 3600);

    // Submit the verification request
    $response = $this->get($verificationUrl);

    // Assert redirect after successful verification
    $response->assertRedirect();

    // Assert user's email has been updated in database
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'new@example.com',
    ]);

    // Assert user has verified email
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects email change verification with invalid signature', function () {
    // Create a user
    $user = User::factory()->create([
        'email' => 'old@example.com',
    ]);

    // Authenticate the user
    Livewire::actingAs($user);

    // Generate invalid verification URL (wrong signature)
    $invalidVerificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-change-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'email' => encrypt('new@example.com'),
        ]
    );

    // Modify the URL to make it invalid
    $invalidVerificationUrl = str_replace('signature=', 'signature=invalid', $invalidVerificationUrl);

    // Submit the verification request with invalid signature
    $response = $this->get($invalidVerificationUrl);

    // Assert verification is rejected with 403 forbidden
    $response->assertForbidden();

    // Assert user's email is still the old one
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'old@example.com',
    ]);
});

it('rejects email change verification with expired signature', function () {
    // Create a user
    $user = User::factory()->create([
        'email' => 'old@example.com',
    ]);

    // Authenticate the user
    Livewire::actingAs($user);

    // Generate expired verification URL
    $expiredVerificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-change-verification.verify',
        now()->subMinutes(61), // Expired 1 minute ago
        [
            'id' => $user->getKey(),
            'email' => encrypt('new@example.com'),
        ]
    );

    // Submit the verification request with expired signature
    $response = $this->get($expiredVerificationUrl);

    // Assert verification is rejected with 403 forbidden
    $response->assertForbidden();

    // Assert user's email is still the old one
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'old@example.com',
    ]);
});
