<?php

use App\Models\User;
use App\Policies\UserPolicy;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;

beforeEach(function () {
    $this->seed(ShieldSeeder::class);
    $this->seed(RoleUserSeeder::class);

    // Get all users created by the seeders
    $this->superAdmin = User::where('email', 'superadmin@filamentum.com')->first();
    $this->admin = User::where('email', 'admin@filamentum.com')->first();
    $this->regularUser = User::where('email', 'user@filamentum.com')->first();
    
    $this->policy = new UserPolicy();
});

it('prevents super admin from deleting themselves', function () {
    $result = $this->policy->delete($this->superAdmin, $this->superAdmin);
    expect($result)->toBeFalse();
});

it('prevents admin from deleting themselves', function () {
    $result = $this->policy->delete($this->admin, $this->admin);
    expect($result)->toBeFalse();
});

it('prevents regular user from deleting themselves', function () {
    $result = $this->policy->delete($this->regularUser, $this->regularUser);
    expect($result)->toBeFalse();
});

it('allows super admin to delete other users', function () {
    $result = $this->policy->delete($this->superAdmin, $this->admin);
    expect($result)->toBeTrue();
});

it('allows admin to delete other users', function () {
    $result = $this->policy->delete($this->admin, $this->regularUser);
    expect($result)->toBeTrue();
});