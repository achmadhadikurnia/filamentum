<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Auth\Notifications\ResetPassword as FilamentResetPasswordNotification;
use Filament\Auth\Pages\Login;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Auth\Pages\PasswordReset\ResetPassword as FilamentResetPassword;
use Filament\Pages\Dashboard;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
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
// Password Reset Request Page Tests
// ------------------------------------------------------------------------------------------------

it('displays password reset request page for guests', function () {
    Livewire::test(RequestPasswordReset::class)
        ->assertSuccessful()
        ->assertSee('Forgot password');
});

it('redirects authenticated super admin away from password reset request page', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(RequestPasswordReset::class)
        ->assertRedirect(Dashboard::getUrl());
});

it('redirects authenticated admin away from password reset request page', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(RequestPasswordReset::class)
        ->assertRedirect(Dashboard::getUrl());
});

it('redirects authenticated regular user away from password reset request page', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(RequestPasswordReset::class)
        ->assertRedirect(Dashboard::getUrl());
});

// ------------------------------------------------------------------------------------------------
// Password Reset Notification Tests
// ------------------------------------------------------------------------------------------------

it('sends password reset notification for super admin', function () {
    Notification::fake();

    $this->superAdmin->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($this->superAdmin, ResetPasswordNotification::class);
});

it('sends password reset notification for admin', function () {
    Notification::fake();

    $this->admin->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($this->admin, ResetPasswordNotification::class);
});

it('sends password reset notification for regular user', function () {
    Notification::fake();

    $this->regularUser->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($this->regularUser, ResetPasswordNotification::class);
});

// ------------------------------------------------------------------------------------------------
// Password Reset Form Tests
// ------------------------------------------------------------------------------------------------

it('displays password reset form for guests with valid token', function () {
    // Create a user for testing
    $user = $this->superAdmin;

    // Fake notifications to capture the reset token
    Notification::fake();

    // Request a password reset
    Livewire::test(RequestPasswordReset::class)
        ->assertSuccessful()
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request');

    // Get the reset token from the notification
    $token = null;
    Notification::assertSentTo($user, FilamentResetPasswordNotification::class, function ($notification, $channels) use ($user, &$token) {
        $token = $notification->token;
        return true;
    });

    // Now test accessing the reset password form with the valid token using Livewire
    Livewire::test(FilamentResetPassword::class, [
        'email' => $user->email,
        'token' => $token
    ])
        ->assertSuccessful()
        ->assertSee('Reset password');
});

it('validates password reset form fields', function () {
    // Create a user for testing
    $user = $this->superAdmin;

    // Fake notifications to capture the reset token
    Notification::fake();

    // Request a password reset
    Livewire::test(RequestPasswordReset::class)
        ->assertSuccessful()
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request');

    // Get the reset token from the notification
    $token = null;
    Notification::assertSentTo($user, FilamentResetPasswordNotification::class, function ($notification, $channels) use ($user, &$token) {
        $token = $notification->token;
        return true;
    });

    // Test password reset form validation using Livewire
    Livewire::test(FilamentResetPassword::class, [
        'email' => $user->email,
        'token' => $token
    ])
        ->assertSuccessful()
        ->fillForm([
            'password' => '', // Empty password
            'passwordConfirmation' => '' // Empty confirmation
        ])
        ->call('resetPassword')
        ->assertHasFormErrors(['password']);
});

it('allows successful password reset with valid data', function () {
    // Create a user for testing
    $user = $this->superAdmin;
    $newPassword = 'new-password123'; // New password

    // Fake notifications to capture the reset token
    Notification::fake();

    // Request a password reset
    Livewire::test(RequestPasswordReset::class)
        ->assertSuccessful()
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request');

    // Get the reset token from the notification
    $token = null;
    Notification::assertSentTo($user, FilamentResetPasswordNotification::class, function ($notification, $channels) use ($user, &$token) {
        $token = $notification->token;
        return true;
    });

    // Test successful password reset using Livewire
    Livewire::test(FilamentResetPassword::class, [
        'email' => $user->email,
        'token' => $token
    ])
        ->assertSuccessful()
        ->fillForm([
            'password' => $newPassword,
            'passwordConfirmation' => $newPassword
        ])
        ->call('resetPassword')
        ->assertHasNoFormErrors();

    // Verify the user can log in with the new password
    Livewire::test(Login::class)
        ->assertSuccessful()
        ->fillForm([
            'email' => $user->email,
            'password' => $newPassword,
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();
});
