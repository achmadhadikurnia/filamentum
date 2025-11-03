<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;

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
// Dashboard Access Tests
// ------------------------------------------------------------------------------------------------

it('redirects unauthenticated users to login page when accessing dashboard', function () {
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.auth.login'));
});

it('allows super admin to access dashboard after authentication', function () {
    Livewire::actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});

it('allows admin to access dashboard after authentication', function () {
    Livewire::actingAs($this->admin);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});

it('allows regular user to access dashboard after authentication', function () {
    Livewire::actingAs($this->regularUser);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});
