<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Pages\Dashboard;
use Illuminate\Auth\Notifications\ResetPassword;
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

    Notification::assertSentTo($this->superAdmin, ResetPassword::class);
});

it('sends password reset notification for admin', function () {
    Notification::fake();

    $this->admin->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($this->admin, ResetPassword::class);
});

it('sends password reset notification for regular user', function () {
    Notification::fake();

    $this->regularUser->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($this->regularUser, ResetPassword::class);
});
