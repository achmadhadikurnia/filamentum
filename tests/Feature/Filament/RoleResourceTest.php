<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
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
    $response->assertOk();
    $response->assertSee('Roles');
});

it('denies admin access to role list', function () {
    Livewire::actingAs($this->admin);

    $response = $this->get(route('filament.app.resources.shield.roles.index'));
    $response->assertForbidden();
});

it('denies regular user access to role list', function () {
    Livewire::actingAs($this->regularUser);

    $response = $this->get(route('filament.app.resources.shield.roles.index'));
    $response->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role List Page Create Button Tests
// ------------------------------------------------------------------------------------------------

it('shows create button for super admin on role list page and allows navigation to create page', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertActionExists('create')
        ->assertActionVisible('create')
        ->callAction('create')
        ->assertSuccessful();
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
    $response->assertOk();
    $response->assertSee('Create Role');
});

it('denies admin access to role creation form', function () {
    Livewire::actingAs($this->admin);

    $response = $this->get(route('filament.app.resources.shield.roles.create'));
    $response->assertForbidden();
});

it('denies regular user access to role creation form', function () {
    Livewire::actingAs($this->regularUser);

    $response = $this->get(route('filament.app.resources.shield.roles.create'));
    $response->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role View Page Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to view role details', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a super admin role's view page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->superAdminRole->id]));
    $response->assertOk();
    $response->assertSee('View Role');

    // Check that the page contains role information
    $response->assertSee($this->superAdminRole->name);

    // Test access to a admin role's view page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->adminRole->id]));
    $response->assertOk();
    $response->assertSee('View Role');

    // Check that the page contains role information
    $response->assertSee($this->adminRole->name);

    // Test access to a regular user role's view page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->userRole->id]));
    $response->assertOk();
    $response->assertSee('View Role');

    // Check that the page contains role information
    $response->assertSee($this->userRole->name);
});

it('denies admin from viewing role details', function () {
    Livewire::actingAs($this->admin);

    // Test that admin cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->superAdminRole->id]));
    $response->assertForbidden();

    // Test that admin cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->adminRole->id]));
    $response->assertForbidden();

    // Test that admin cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->userRole->id]));
    $response->assertForbidden();
});

it('denies regular user from viewing role details', function () {
    Livewire::actingAs($this->regularUser);

    // Test that regular user cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->superAdminRole->id]));
    $response->assertForbidden();

    // Test that regular user cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->adminRole->id]));
    $response->assertForbidden();

    // Test that regular user cannot view role page
    $response = $this->get(route('filament.app.resources.shield.roles.view', ['record' => $this->userRole->id]));
    $response->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role Edit Page Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to edit role details', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a super admin role's edit page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->superAdminRole->id]));
    $response->assertOk();
    $response->assertSee('Edit Role');

    // Check that the page contains role information
    $response->assertSee($this->superAdminRole->name);

    // Test access to a admin role's edit page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->adminRole->id]));
    $response->assertOk();
    $response->assertSee('Edit Role');

    // Check that the page contains role information
    $response->assertSee($this->adminRole->name);

    // Test access to a regular user role's edit page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->userRole->id]));
    $response->assertOk();
    $response->assertSee('Edit Role');

    // Check that the page contains role information
    $response->assertSee($this->userRole->name);
});

it('denies admin from editing role details', function () {
    Livewire::actingAs($this->admin);

    // Test that admin cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->superAdminRole->id]));
    $response->assertForbidden();

    // Test that admin cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->adminRole->id]));
    $response->assertForbidden();

    // Test that admin cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->userRole->id]));
    $response->assertForbidden();
});

it('denies regular user from editing role details', function () {
    Livewire::actingAs($this->regularUser);

    // Test that regular user cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->superAdminRole->id]));
    $response->assertForbidden();

    // Test that regular user cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->adminRole->id]));
    $response->assertForbidden();

    // Test that regular user cannot edit role page
    $response = $this->get(route('filament.app.resources.shield.roles.edit', ['record' => $this->userRole->id]));
    $response->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role List Page Edit Button Tests
// ------------------------------------------------------------------------------------------------

it('shows edit button for super admin on role list page and allows navigation to edit page', function () {
    Livewire::actingAs($this->superAdmin);

    // Check that super admin can see edit button for all roles and can navigate to edit page
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionExists('edit')
        ->assertTableActionVisible('edit', $this->superAdminRole)
        ->callTableAction('edit', $this->superAdminRole)
        ->assertSuccessful();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionExists('edit')
        ->assertTableActionVisible('edit', $this->adminRole)
        ->callTableAction('edit', $this->adminRole)
        ->assertSuccessful();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionExists('edit')
        ->assertTableActionVisible('edit', $this->userRole)
        ->callTableAction('edit', $this->userRole)
        ->assertSuccessful();
});

it('hides edit button for admin on role list page', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});

it('hides edit button for regular user on role list page', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role View Page Edit Button Tests
// ------------------------------------------------------------------------------------------------

it('shows edit button for super admin on role view page and allows navigation to edit page', function () {
    Livewire::actingAs($this->superAdmin);

    // Check that super admin can see edit button on view page for all roles and can navigate to edit page
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertActionExists('edit')
        ->assertActionVisible('edit')
        ->callAction('edit')
        ->assertSuccessful();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->adminRole->id])
        ->assertActionExists('edit')
        ->assertActionVisible('edit')
        ->callAction('edit')
        ->assertSuccessful();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->userRole->id])
        ->assertActionExists('edit')
        ->assertActionVisible('edit')
        ->callAction('edit')
        ->assertSuccessful();
});

it('hides edit button for admin on role view page', function () {
    Livewire::actingAs($this->admin);

    // Check that admin cannot see edit button on view page
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

it('hides edit button for regular user on role view page', function () {
    Livewire::actingAs($this->regularUser);

    // Check that regular user cannot see edit button on view page
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role List Page Delete Button Tests
// ------------------------------------------------------------------------------------------------

it('shows delete button for super admin on role list page for admin and user roles and allows deletion', function () {
    Livewire::actingAs($this->superAdmin);

    // Check that super admin can see delete button for admin roles and can delete them
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionExists('delete')
        ->assertTableActionVisible('delete', $this->adminRole)
        ->callTableAction('delete', $this->adminRole)
        ->assertSuccessful();

    // Verify admin role was deleted
    $this->assertDatabaseMissing('roles', ['id' => $this->adminRole->id]);

    // Check that super admin can see delete button for user roles and can delete them
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionExists('delete')
        ->assertTableActionVisible('delete', $this->userRole)
        ->callTableAction('delete', $this->userRole)
        ->assertSuccessful();

    // Verify user role was deleted
    $this->assertDatabaseMissing('roles', ['id' => $this->userRole->id]);
});

it('hides delete button for super admin on role list page for super admin role', function () {
    Livewire::actingAs($this->superAdmin);

    // Check that super admin cannot see delete button for super admin role
    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertTableActionHidden('delete', $this->superAdminRole);
});

it('hides delete button for admin on role list page', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});

it('hides delete button for regular user on role list page', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles::class)
        ->assertForbidden();
});
