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
// Registration Access Tests
// ------------------------------------------------------------------------------------------------

it('displays the registration page for guests', function () {
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertOk();
    $response->assertSee('Register');
    $response->assertSee('Name');
    $response->assertSee('Email');
    $response->assertSee('Password');
});

it('redirects authenticated super admin away from register page', function () {
    Livewire::actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated admin away from register page', function () {
    Livewire::actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated regular user away from register page', function () {
    Livewire::actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});
