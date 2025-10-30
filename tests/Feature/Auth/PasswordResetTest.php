<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

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
    $response = $this->get(route('filament.app.auth.password-reset.request'));

    $response->assertStatus(200);
    $response->assertSee('Forgot password');
    $response->assertSee('email');
});

it('redirects authenticated super admin away from password reset request page', function () {
    $this->actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.password-reset.request'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated admin away from password reset request page', function () {
    $this->actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.password-reset.request'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated regular user away from password reset request page', function () {
    $this->actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.password-reset.request'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

// // ------------------------------------------------------------------------------------------------
// // Password Reset Form Page Tests
// // ------------------------------------------------------------------------------------------------

// it('has password reset form page', function () {
//     $resetRoute = route('filament.app.auth.password-reset.reset', ['token' => 'test-token', 'email' => 'test@example.com']);

//     expect($resetRoute)->toBeString();
//     expect($resetRoute)->toContain('password-reset');
// });

// // ------------------------------------------------------------------------------------------------
// // Password Reset Route Tests
// // ------------------------------------------------------------------------------------------------

// it('has working password reset routes', function () {
//     $requestRoute = route('filament.app.auth.password-reset.request');
//     $resetRoute = route('filament.app.auth.password-reset.reset', ['token' => 'test-token', 'email' => 'test@example.com']);

//     expect($requestRoute)->toBeString();
//     expect($resetRoute)->toBeString();
//     expect($requestRoute)->toContain('password-reset');
//     expect($resetRoute)->toContain('password-reset');
// });

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
