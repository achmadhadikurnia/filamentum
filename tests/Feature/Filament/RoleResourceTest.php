<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Seed the database with roles, permissions, and users
    $this->seed(ShieldSeeder::class);
    $this->seed(RoleUserSeeder::class);

    // Get all test users created by the seeders
    $this->superAdmin = User::where('email', 'superadmin@filamentum.com')->first();
    $this->admin = User::where('email', 'admin@filamentum.com')->first();
    $this->regularUser = User::where('email', 'user@filamentum.com')->first();

    // Get roles
    $this->superAdminRole = Role::findByName('Super Admin');
    $this->adminRole = Role::findByName('Admin');
    $this->userRole = Role::findByName('User');
});

// ------------------------------------------------------------------------------------------------
// Role List Page Tests
// ------------------------------------------------------------------------------------------------

it('displays correct role data on super admin role list', function () {
    Livewire::actingAs($this->superAdmin);

    $response = $this->get(route('filament.app.resources.shield.roles.index'));
    $response->assertStatus(200);
    $response->assertSee('Roles');
});

it('denies admin access to role list', function () {
    Livewire::actingAs($this->admin);

    $response = $this->get(route('filament.app.resources.shield.roles.index'));
    $response->assertStatus(403); // Forbidden
});

it('denies regular user access to role list', function () {
    Livewire::actingAs($this->regularUser);

    $response = $this->get(route('filament.app.resources.shield.roles.index'));
    $response->assertStatus(403); // Forbidden
});

// ------------------------------------------------------------------------------------------------
// Role List Page Create Button Tests
// ------------------------------------------------------------------------------------------------

it('shows create button for super admin on role list page', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertActionExists('create')
        ->assertActionVisible('create');
});

it('hides create button for admin on role list page', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});

it('hides create button for regular user on role list page', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role Creation Page Tests
// ------------------------------------------------------------------------------------------------

it('displays role creation form for super admin', function () {
    Livewire::actingAs($this->superAdmin);

    $response = $this->get(route('filament.app.resources.shield.roles.create'));
    $response->assertStatus(200);
    $response->assertSee('Create Role');
});

it('denies admin access to role creation form', function () {
    Livewire::actingAs($this->admin);

    $response = $this->get(route('filament.app.resources.shield.roles.create'));
    $response->assertStatus(403); // Forbidden
});

it('denies regular user access to role creation form', function () {
    Livewire::actingAs($this->regularUser);

    $response = $this->get(route('filament.app.resources.shield.roles.create'));
    $response->assertStatus(403); // Forbidden
});

// ------------------------------------------------------------------------------------------------
// Role View Page Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to view role details', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a super admin role's view page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->superAdminRole->id]));
    $response->assertStatus(200);
    $response->assertSee('View Role');

    // Check that the page contains role information
    $response->assertSee($this->superAdminRole->name);

    // Test access to a admin role's view page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->adminRole->id]));
    $response->assertStatus(200);
    $response->assertSee('View Role');

    // Check that the page contains role information
    $response->assertSee($this->adminRole->name);

    // Test access to a regular user role's view page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->userRole->id]));
    $response->assertStatus(200);
    $response->assertSee('View Role');

    // Check that the page contains role information
    $response->assertSee($this->userRole->name);
});

it('denies admin from viewing role details', function () {
    Livewire::actingAs($this->admin);

    // Test that admin cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->superAdminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that admin cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->adminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that admin cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->userRole->id]));
    $response->assertStatus(403); // Forbidden
});

it('denies regular user from viewing role details', function () {
    Livewire::actingAs($this->regularUser);

    // Test that regular user cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->superAdminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that regular user cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->adminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that regular user cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->userRole->id]));
    $response->assertStatus(403); // Forbidden
});

// ------------------------------------------------------------------------------------------------
// Role Edit Page Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to edit role details', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a super admin role's edit page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->superAdminRole->id]));
    $response->assertStatus(200);
    $response->assertSee('Edit Role');

    // Check that the page contains role information
    $response->assertSee($this->superAdminRole->name);

    // Test access to a admin role's edit page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->adminRole->id]));
    $response->assertStatus(200);
    $response->assertSee('Edit Role');

    // Check that the page contains role information
    $response->assertSee($this->adminRole->name);

    // Test access to a regular user role's edit page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->userRole->id]));
    $response->assertStatus(200);
    $response->assertSee('Edit Role');

    // Check that the page contains role information
    $response->assertSee($this->userRole->name);
});

it('denies admin from editing role details', function () {
    Livewire::actingAs($this->admin);

    // Test that admin cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->superAdminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that admin cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->adminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that admin cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->userRole->id]));
    $response->assertStatus(403); // Forbidden
});

it('denies regular user from editing role details', function () {
    Livewire::actingAs($this->regularUser);

    // Test that regular user cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->superAdminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that regular user cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->adminRole->id]));
    $response->assertStatus(403); // Forbidden

    // Test that regular user cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->userRole->id]));
    $response->assertStatus(403); // Forbidden
});

// ------------------------------------------------------------------------------------------------
// Role Deletion Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to delete admin and user roles', function () {
    Livewire::actingAs($this->superAdmin);

    // Test deleting admin role through the list page
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionExists('delete')
        ->assertTableActionVisible('delete', $this->adminRole)
        ->callTableAction('delete', $this->adminRole)
        ->assertSuccessful();

    // Verify admin role was deleted
    $this->assertDatabaseMissing('roles', ['id' => $this->adminRole->id]);

    // Test deleting user role through the list page
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionExists('delete')
        ->assertTableActionVisible('delete', $this->userRole)
        ->callTableAction('delete', $this->userRole)
        ->assertSuccessful();

    // Verify user role was deleted
    $this->assertDatabaseMissing('roles', ['id' => $this->userRole->id]);
});

it('prevents super admin from deleting super admin role', function () {
    Livewire::actingAs($this->superAdmin);

    // Attempt to delete super admin role should be prevented by policy
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionHidden('delete', $this->superAdminRole);

    // Verify super admin role still exists
    $this->assertDatabaseHas('roles', ['id' => $this->superAdminRole->id]);
});

it('denies admin from deleting any roles', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});

it('denies user from deleting any roles', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});
