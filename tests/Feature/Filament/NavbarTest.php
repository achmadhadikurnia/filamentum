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
// Navbar Access Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to access dashboard with navbar', function () {
    $this->actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('Users');
});

it('allows admin to access dashboard with navbar', function () {
    $this->actingAs($this->admin);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('Users');
});

it('allows regular user to access dashboard with navbar', function () {
    $this->actingAs($this->regularUser);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
});
