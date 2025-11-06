<?php

use App\Models\User;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\CreateRole;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\EditRole;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertSee('Roles');
});

it('denies admin access to role list', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

it('denies regular user access to role list', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role List Page Create Button Tests
// ------------------------------------------------------------------------------------------------

it('shows create button for super admin on role list page', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertActionExists('create')
        ->assertActionVisible('create')
        ->callAction('create')
        ->assertSuccessful();
});

it('hides create button for admin on role list page', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

it('hides create button for regular user on role list page', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role Creation Page Tests
// ------------------------------------------------------------------------------------------------

it('displays role creation form for super admin', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(CreateRole::class)
        ->assertSuccessful()
        ->assertSee('Create Role');
});

it('denies admin access to role creation form', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(CreateRole::class)
        ->assertForbidden();
});

it('denies regular user access to role creation form', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(CreateRole::class)
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role Creation Form Validation Tests
// ------------------------------------------------------------------------------------------------

it('validates role name is required', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(CreateRole::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => '',
            'guard_name' => 'web',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('validates role name is unique', function () {
    Livewire::actingAs($this->superAdmin);

    // Create a role first
    Role::create(['name' => 'test-role', 'guard_name' => 'web']);

    Livewire::test(CreateRole::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => 'test-role', // Same name as existing role
            'guard_name' => 'web',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('validates role name max length', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(CreateRole::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => str_repeat('a', 256), // Exceeds max length of 255
            'guard_name' => 'web',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'max']);
});

// ------------------------------------------------------------------------------------------------
// Role Creation Success Tests
// ------------------------------------------------------------------------------------------------

it('allows valid role creation', function () {
    Livewire::actingAs($this->superAdmin);

    $roleName = 'new-test-role';

    Livewire::test(CreateRole::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => $roleName,
            'guard_name' => 'web',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertSuccessful();

    // Verify the role was created in the database
    $this->assertDatabaseHas('roles', [
        'name' => $roleName,
        'guard_name' => 'web',
    ]);

    // Verify we were redirected to the edit page
    $newRole = Role::where('name', $roleName)->first();
    expect($newRole)->not->toBeNull();
});

// ------------------------------------------------------------------------------------------------
// Role View Page Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to view role details', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a super admin role's view page
    Livewire::test(ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertSuccessful()
        ->assertSee('View Role');

    // Test access to a admin role's view page
    Livewire::test(ViewRole::class, ['record' => $this->adminRole->id])
        ->assertSuccessful()
        ->assertSee('View Role');

    // Test access to a regular user role's view page
    Livewire::test(ViewRole::class, ['record' => $this->userRole->id])
        ->assertSuccessful()
        ->assertSee('View Role');
});

it('denies admin from viewing role details', function () {
    Livewire::actingAs($this->admin);

    // Test that admin cannot view role page
    Livewire::test(ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    // Test that admin cannot view role page
    Livewire::test(ViewRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    // Test that admin cannot view role page
    Livewire::test(ViewRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

it('denies regular user from viewing role details', function () {
    Livewire::actingAs($this->regularUser);

    // Test that regular user cannot view role page
    Livewire::test(ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    // Test that regular user cannot view role page
    Livewire::test(ViewRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    // Test that regular user cannot view role page
    Livewire::test(ViewRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

it('does not allow viewing a missing role record', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a non-existent role's view page
    $this->expectException(ModelNotFoundException::class);
    Livewire::test(ViewRole::class, ['record' => 999999]);
});

// ------------------------------------------------------------------------------------------------
// Role Edit Page Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to edit role details', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a super admin role's edit page
    Livewire::test(EditRole::class, ['record' => $this->superAdminRole->id])
        ->assertSuccessful()
        ->assertSee('Edit Role');

    // Test access to a admin role's edit page
    Livewire::test(EditRole::class, ['record' => $this->adminRole->id])
        ->assertSuccessful()
        ->assertSee('Edit Role');

    // Test access to a regular user role's edit page
    Livewire::test(EditRole::class, ['record' => $this->userRole->id])
        ->assertSuccessful()
        ->assertSee('Edit Role');
});

it('denies admin from editing role details', function () {
    Livewire::actingAs($this->admin);

    // Test that admin cannot edit role page
    Livewire::test(EditRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    // Test that admin cannot edit role page
    Livewire::test(EditRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    // Test that admin cannot edit role page
    Livewire::test(EditRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

it('denies regular user from editing role details', function () {
    Livewire::actingAs($this->regularUser);

    // Test that regular user cannot edit role page
    Livewire::test(EditRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    // Test that regular user cannot edit role page
    Livewire::test(EditRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    // Test that regular user cannot edit role page
    Livewire::test(EditRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

it('does not allow editing a missing role record', function () {
    Livewire::actingAs($this->superAdmin);

    // Test access to a non-existent role's edit page
    $this->expectException(ModelNotFoundException::class);
    Livewire::test(EditRole::class, ['record' => 999999]);
});

// ------------------------------------------------------------------------------------------------
// Role List Page Edit Button Tests
// ------------------------------------------------------------------------------------------------

it('shows edit button for super admin on role list page', function () {
    Livewire::actingAs($this->superAdmin);

    // Check that super admin can see edit button for all roles
    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertTableActionExists('edit')
        ->assertTableActionVisible('edit', $this->superAdminRole)
        ->callTableAction('edit', $this->superAdminRole)
        ->assertSuccessful();

    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertTableActionExists('edit')
        ->assertTableActionVisible('edit', $this->adminRole)
        ->callTableAction('edit', $this->adminRole)
        ->assertSuccessful();

    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertTableActionExists('edit')
        ->assertTableActionVisible('edit', $this->userRole)
        ->callTableAction('edit', $this->userRole)
        ->assertSuccessful();
});

it('hides edit button for admin on role list page', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

it('hides edit button for regular user on role list page', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role View Page Edit Button Tests
// ------------------------------------------------------------------------------------------------

it('shows edit button for super admin on role view page', function () {
    Livewire::actingAs($this->superAdmin);

    // Check that super admin can see edit button on view page for all roles
    Livewire::test(ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertSuccessful()
        ->assertActionExists('edit')
        ->assertActionVisible('edit')
        ->callAction('edit')
        ->assertSuccessful();

    Livewire::test(ViewRole::class, ['record' => $this->adminRole->id])
        ->assertSuccessful()
        ->assertActionExists('edit')
        ->assertActionVisible('edit')
        ->callAction('edit')
        ->assertSuccessful();

    Livewire::test(ViewRole::class, ['record' => $this->userRole->id])
        ->assertSuccessful()
        ->assertActionExists('edit')
        ->assertActionVisible('edit')
        ->callAction('edit')
        ->assertSuccessful();
});

it('hides edit button for admin on role view page', function () {
    Livewire::actingAs($this->admin);

    // Check that admin cannot see edit button on view page
    Livewire::test(ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    Livewire::test(ViewRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    Livewire::test(ViewRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

it('hides edit button for regular user on role view page', function () {
    Livewire::actingAs($this->regularUser);

    // Check that regular user cannot see edit button on view page
    Livewire::test(ViewRole::class, ['record' => $this->superAdminRole->id])
        ->assertForbidden();

    Livewire::test(ViewRole::class, ['record' => $this->adminRole->id])
        ->assertForbidden();

    Livewire::test(ViewRole::class, ['record' => $this->userRole->id])
        ->assertForbidden();
});

// ------------------------------------------------------------------------------------------------
// Role Update Form Validation Tests
// ------------------------------------------------------------------------------------------------

it('validates role name is required on update', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(EditRole::class, ['record' => $this->adminRole->id])
        ->assertSuccessful()
        ->fillForm([
            'name' => '',
            'guard_name' => 'web',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

it('validates role name is unique on update', function () {
    Livewire::actingAs($this->superAdmin);

    // Create another role
    Role::create(['name' => 'other-role', 'guard_name' => 'web']);

    Livewire::test(EditRole::class, ['record' => $this->adminRole->id])
        ->assertSuccessful()
        ->fillForm([
            'name' => 'other-role', // Same name as existing role
            'guard_name' => 'web',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('validates role name max length on update', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(EditRole::class, ['record' => $this->adminRole->id])
        ->assertSuccessful()
        ->fillForm([
            'name' => str_repeat('a', 256), // Exceeds max length of 255
            'guard_name' => 'web',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'max']);
});

// ------------------------------------------------------------------------------------------------
// Role Update Success Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to update role name', function () {
    Livewire::actingAs($this->superAdmin);

    $newRoleName = 'updated-admin-role';

    Livewire::test(EditRole::class, ['record' => $this->adminRole->id])
        ->assertSuccessful()
        ->fillForm([
            'name' => $newRoleName,
            'guard_name' => 'web',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSuccessful();

    // Verify the role was updated in the database
    $this->assertDatabaseHas('roles', [
        'id' => $this->adminRole->id,
        'name' => $newRoleName,
        'guard_name' => 'web',
    ]);

    // Verify the old name no longer exists
    $this->assertDatabaseMissing('roles', [
        'id' => $this->adminRole->id,
        'name' => $this->adminRole->name,
    ]);
});

// ------------------------------------------------------------------------------------------------
// Role List Page Delete Button Tests
// ------------------------------------------------------------------------------------------------

it('shows delete button for super admin on role list page for admin and user roles and allows deletion', function () {
    Livewire::actingAs($this->superAdmin);

    // Check that super admin can see delete button for admin roles and can delete them
    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertTableActionExists('delete')
        ->assertTableActionVisible('delete', $this->adminRole)
        ->callTableAction('delete', $this->adminRole)
        ->assertSuccessful();

    // Verify admin role was deleted
    $this->assertDatabaseMissing('roles', ['id' => $this->adminRole->id]);

    // Check that super admin can see delete button for user roles and can delete them
    Livewire::test(ListRoles::class)
        ->assertSuccessful()
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
    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertTableActionHidden('delete', $this->superAdminRole);
});

it('hides delete button for admin on role list page', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});

it('hides delete button for regular user on role list page', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(ListRoles::class)
        ->assertForbidden();
});
