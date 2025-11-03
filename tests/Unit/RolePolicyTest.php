<?php

use App\Models\User;
use App\Policies\RolePolicy;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
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

    // Create policy instance
    $this->rolePolicy = new RolePolicy;
});

// ------------------------------------------------------------------------------------------------
// Role Deletion Policy Tests
// ------------------------------------------------------------------------------------------------

it('prevents anyone from deleting the Super Admin role', function () {
    // Test that even super admin cannot delete Super Admin role
    expect($this->rolePolicy->delete($this->superAdmin, $this->superAdminRole))->toBeFalse();

    // Test that admin cannot delete Super Admin role
    expect($this->rolePolicy->delete($this->admin, $this->superAdminRole))->toBeFalse();

    // Test that regular user cannot delete Super Admin role
    expect($this->rolePolicy->delete($this->regularUser, $this->superAdminRole))->toBeFalse();
});

it('allows super admin to delete other roles', function () {
    // Test that super admin can delete Admin role
    expect($this->rolePolicy->delete($this->superAdmin, $this->adminRole))->toBeTrue();

    // Test that super admin can delete User role
    expect($this->rolePolicy->delete($this->superAdmin, $this->userRole))->toBeTrue();
});

it('denies admin from deleting any roles', function () {
    // Test that admin cannot delete Admin role
    expect($this->rolePolicy->delete($this->admin, $this->adminRole))->toBeFalse();

    // Test that admin cannot delete User role
    expect($this->rolePolicy->delete($this->admin, $this->userRole))->toBeFalse();
});

it('denies regular user from deleting any roles', function () {
    // Test that regular user cannot delete Admin role
    expect($this->rolePolicy->delete($this->regularUser, $this->adminRole))->toBeFalse();

    // Test that regular user cannot delete User role
    expect($this->rolePolicy->delete($this->regularUser, $this->userRole))->toBeFalse();
});
