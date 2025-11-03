<?php

use App\Models\User;
use App\Policies\UserPolicy;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;

beforeEach(function () {
    // Seed the database with roles, permissions, and users
    $this->seed(ShieldSeeder::class);
    $this->seed(RoleUserSeeder::class);

    // Get all users created by the seeders
    $this->superAdmin = User::where('email', 'superadmin@filamentum.com')->first();
    $this->admin = User::where('email', 'admin@filamentum.com')->first();
    $this->regularUser = User::where('email', 'user@filamentum.com')->first();

    $this->policy = new UserPolicy;
});

// ------------------------------------------------------------------------------------------------
// User Deletion Policy Tests
// ------------------------------------------------------------------------------------------------

it('prevents super admin from deleting themselves', function () {
    $result = $this->policy->delete($this->superAdmin, $this->superAdmin);
    expect($result)->toBeFalse();
});

it('allows super admin to delete other super admin users', function () {
    // Create another super admin user for testing
    $anotherSuperAdmin = User::factory()->create();
    $anotherSuperAdmin->assignRole('Super Admin');

    $result = $this->policy->delete($this->superAdmin, $anotherSuperAdmin);
    expect($result)->toBeTrue();
});

it('allows super admin to delete admin users', function () {
    $result = $this->policy->delete($this->superAdmin, $this->admin);
    expect($result)->toBeTrue();
});

it('allows super admin to delete regular users', function () {
    $result = $this->policy->delete($this->superAdmin, $this->regularUser);
    expect($result)->toBeTrue();
});

it('prevents admin from deleting super admin', function () {
    $result = $this->policy->delete($this->admin, $this->superAdmin);
    expect($result)->toBeFalse();
});

it('prevents admin from deleting themselves', function () {
    $result = $this->policy->delete($this->admin, $this->admin);
    expect($result)->toBeFalse();
});

it('allows admin to delete regular users', function () {
    $result = $this->policy->delete($this->admin, $this->regularUser);
    expect($result)->toBeTrue();
});

it('prevents regular user from deleting super admin', function () {
    $result = $this->policy->delete($this->regularUser, $this->superAdmin);
    expect($result)->toBeFalse();
});

it('prevents regular user from deleting admin', function () {
    $result = $this->policy->delete($this->regularUser, $this->admin);
    expect($result)->toBeFalse();
});

it('prevents regular user from deleting themselves', function () {
    $result = $this->policy->delete($this->regularUser, $this->regularUser);
    expect($result)->toBeFalse();
});

// ------------------------------------------------------------------------------------------------
// User Update Policy Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to update users', function () {
    $result = $this->policy->update($this->superAdmin);
    expect($result)->toBeTrue();
});

it('allows admin to update users', function () {
    $result = $this->policy->update($this->admin);
    expect($result)->toBeTrue();
});

it('prevents regular user from updating users', function () {
    $result = $this->policy->update($this->regularUser);
    expect($result)->toBeFalse();
});
