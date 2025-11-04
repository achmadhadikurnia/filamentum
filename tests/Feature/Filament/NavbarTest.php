<?php

use App\Models\User;
use App\Filament\Resources\Users\Pages\ListUsers;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Pages\Dashboard;
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
// Navbar Access Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to access and view the list menu', function () {
    Livewire::actingAs($this->superAdmin);

    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertSuccessful()
        ->assertSee('Dashboard')
        ->assertSee('Users')
        ->assertSee('Roles');
});

it('allows admin to access and view the list menu', function () {
    Livewire::actingAs($this->admin);

    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertSuccessful()
        ->assertSee('Dashboard')
        ->assertSee('Users');
});

it('allows regular user to access and view the list menu', function () {
    Livewire::actingAs($this->regularUser);

    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertSuccessful()
        ->assertSee('Dashboard');
});

// ------------------------------------------------------------------------------------------------
// Dashboard Menu Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to access dashboard with navbar using livewire', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(Dashboard::class)
        ->assertSuccessful()
        ->assertSee('Dashboard');
});

it('allows admin to access dashboard with navbar using livewire', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(Dashboard::class)
        ->assertSuccessful()
        ->assertSee('Dashboard');
});

it('allows regular user to access dashboard with navbar using livewire', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(Dashboard::class)
        ->assertSuccessful()
        ->assertSee('Dashboard');
});

// ------------------------------------------------------------------------------------------------
// User Management Menu Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to access user management menu using livewire', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(ListUsers::class)
        ->assertSuccessful()
        ->assertSee('Users');
});

it('allows admin to access user management menu using livewire', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(ListUsers::class)
        ->assertSuccessful()
        ->assertSee('Users');
});

it('denies regular user access to user management menu using livewire', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(ListUsers::class)
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role Management Menu Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to access role management menu using livewire', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertSee('Roles');
});

it('denies admin access to role management menu using livewire', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

it('denies regular user access to role management menu using livewire', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});
