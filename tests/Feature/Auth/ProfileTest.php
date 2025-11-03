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
// Profile Access Tests
// ------------------------------------------------------------------------------------------------

it('redirects unauthenticated users to login when accessing profile', function () {
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.auth.login'));
});

it('displays the profile page for super admin', function () {
    Livewire::actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee('Profile');
    $response->assertSee($this->superAdmin->name);
    $response->assertSee($this->superAdmin->email);
});

it('displays the profile page for admin', function () {
    Livewire::actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee('Profile');
    $response->assertSee($this->admin->name);
    $response->assertSee($this->admin->email);
});

it('displays the profile page for regular user', function () {
    Livewire::actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee('Profile');
    $response->assertSee($this->regularUser->name);
    $response->assertSee($this->regularUser->email);
});
