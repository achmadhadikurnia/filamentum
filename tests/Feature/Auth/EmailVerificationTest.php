<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

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

    $this->actingAs($user);
    $response = $this->get(route('filament.app.auth.email-verification.prompt'));

    $response->assertStatus(200);
    $response->assertSee('Verify your email address');
});

it('redirects verified super admin away from email verification prompt', function () {
    Livewire::actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.email-verification.prompt'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects verified admin away from email verification prompt', function () {
    Livewire::actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.email-verification.prompt'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects verified regular user away from email verification prompt', function () {
    Livewire::actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.email-verification.prompt'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
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
// Email Verification Process Tests
// ------------------------------------------------------------------------------------------------

it('can verify email with valid signature for super admin', function () {
    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    // Assign admin role
    $user->assignRole('Super Admin');

    $this->actingAs($user);

    // Generate verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $response = $this->get($verificationUrl);

    // Check that the user is now verified
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();

    // Check redirect to dashboard
    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('can verify email with valid signature for admin', function () {
    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    // Assign admin role
    $user->assignRole('Admin');

    $this->actingAs($user);

    // Generate verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $response = $this->get($verificationUrl);

    // Check that the user is now verified
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();

    // Check redirect to dashboard
    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('can verify email with valid signature for regular user', function () {
    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    // Assign user role
    $user->assignRole('User');

    $this->actingAs($user);

    // Generate verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $response = $this->get($verificationUrl);

    // Check that the user is now verified
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();

    // Check redirect to dashboard
    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('cannot verify email with invalid signature', function () {
    // Create an unverified user
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user);

    // Generate invalid verification URL (wrong hash)
    $invalidVerificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => 'invalid-hash',
        ]
    );

    $response = $this->get($invalidVerificationUrl);

    // Check that the user is still not verified
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();

    // Check that we get a forbidden response for invalid signatures
    $response->assertStatus(403);
});
