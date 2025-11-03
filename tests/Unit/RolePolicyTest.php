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

    // Get all roles created by the seeders
    $this->superAdminRole = Role::findByName('Super Admin');
    $this->adminRole = Role::findByName('Admin');
    $this->userRole = Role::findByName('User');

    // Get a user to test with
    $this->superAdmin = User::where('email', 'superadmin@filamentum.com')->first();

    $this->policy = new RolePolicy;
});

// ------------------------------------------------------------------------------------------------
// Role Deletion Policy Tests
// ------------------------------------------------------------------------------------------------

it('prevents deletion of Super Admin role', function () {
    $result = $this->policy->delete($this->superAdmin, $this->superAdminRole);
    expect($result)->toBeFalse();
});

it('allows deletion of Admin role', function () {
    $result = $this->policy->delete($this->superAdmin, $this->adminRole);
    expect($result)->toBeTrue();
});

it('allows deletion of User role', function () {
    $result = $this->policy->delete($this->superAdmin, $this->userRole);
    expect($result)->toBeTrue();
});

it('allows deletion of other roles', function () {
    // Create a test role for deletion
    $testRole = Role::create(['name' => 'Test Role']);

    $result = $this->policy->delete($this->superAdmin, $testRole);
    expect($result)->toBeTrue();
});
