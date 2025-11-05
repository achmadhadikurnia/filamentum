<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Dashboard;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
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
// Email Verification Prompt Tests
// ------------------------------------------------------------------------------------------------

it('displays email verification prompt for unverified users', function () {
    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    Livewire::actingAs($user);

    Livewire::test(EmailVerificationPrompt::class)
        ->assertSuccessful()
        ->assertSee('Verify your email address');
});

it('redirects verified super admin away from email verification prompt', function () {
    Livewire::actingAs($this->superAdmin);

    $response = $this->get(route('filament.app.auth.email-verification.prompt'));

    $response->assertRedirect(Dashboard::getUrl());
});

it('redirects verified admin away from email verification prompt', function () {
    Livewire::actingAs($this->admin);

    $response = $this->get(route('filament.app.auth.email-verification.prompt'));

    $response->assertRedirect(Dashboard::getUrl());
});

it('redirects verified regular user away from email verification prompt', function () {
    Livewire::actingAs($this->regularUser);

    $response = $this->get(route('filament.app.auth.email-verification.prompt'));

    $response->assertRedirect(Dashboard::getUrl());
});

// ------------------------------------------------------------------------------------------------
// Email Verification Notification Tests
// ------------------------------------------------------------------------------------------------

it('sends email verification notification', function () {
    Notification::fake();

    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    // Send verification email
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
});

// ------------------------------------------------------------------------------------------------
// Email Verification Form Process Tests
// ------------------------------------------------------------------------------------------------

it('allows successful email verification process with valid data', function () {
    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    // Assign user role
    $user->assignRole('User');

    Livewire::actingAs($user);

    // Generate verification URL with valid signature
    $verificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    // Submit the verification request
    $response = $this->get($verificationUrl);

    // Assert successful verification
    $response->assertRedirect(Dashboard::getUrl());

    // Assert user is now verified in database
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);

    // Assert user has verified email
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects email verification with invalid signature', function () {
    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    Livewire::actingAs($user);

    // Generate invalid verification URL (wrong hash)
    $invalidVerificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => 'invalid-hash',
        ]
    );

    // Submit the verification request with invalid signature
    $response = $this->get($invalidVerificationUrl);

    // Assert verification is rejected
    $response->assertForbidden();

    // Assert user is still not verified in database
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email_verified_at' => null,
    ]);

    // Assert user still has unverified email
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
