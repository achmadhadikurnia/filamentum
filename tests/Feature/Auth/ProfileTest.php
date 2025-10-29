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
// Profile Access Tests
// ------------------------------------------------------------------------------------------------

it('redirects unauthenticated users to login when accessing profile', function () {
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.auth.login'));
});

it('displays profile page with correct content for super admin', function () {
    $this->actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee('Profile');
    $response->assertSee($this->superAdmin->name);
    $response->assertSee($this->superAdmin->email);
});

it('displays profile page with correct content for admin', function () {
    $this->actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee('Profile');
    $response->assertSee($this->admin->name);
    $response->assertSee($this->admin->email);
});

it('displays profile page with correct content for regular user', function () {
    $this->actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee('Profile');
    $response->assertSee($this->regularUser->name);
    $response->assertSee($this->regularUser->email);
});

// ------------------------------------------------------------------------------------------------
// User Isolation Tests
// ------------------------------------------------------------------------------------------------

it('ensures super admin can only see their own profile information', function () {
    $this->actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee($this->superAdmin->name);
    $response->assertSee($this->superAdmin->email);
});

it('ensures admin can only see their own profile information', function () {
    $this->actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee($this->admin->name);
    $response->assertSee($this->admin->email);
});

it('ensures regular user can only see their own profile information', function () {
    $this->actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee($this->regularUser->name);
    $response->assertSee($this->regularUser->email);
});

// ------------------------------------------------------------------------------------------------
// Authentication State Tests
// ------------------------------------------------------------------------------------------------

it('redirects to login after user logs out', function () {
    // Given: A user is logged in and can access their profile
    $this->actingAs($this->superAdmin);
    $profileResponse = $this->get(route('filament.app.auth.profile'));
    $profileResponse->assertStatus(200);

    // When: The user logs out
    $logoutResponse = $this->post(route('filament.app.auth.logout'));
    $logoutResponse->assertStatus(302);
    $this->assertGuest();

    // Then: The user is redirected to login when trying to access profile again
    $redirectedProfileResponse = $this->get(route('filament.app.auth.profile'));
    $redirectedProfileResponse->assertStatus(302);
    $redirectedProfileResponse->assertRedirect(route('filament.app.auth.login'));
});

it('allows super admin to access profile after authentication', function () {
    $this->actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee($this->superAdmin->name);
    $response->assertSee($this->superAdmin->email);
});

it('allows admin to access profile after authentication', function () {
    $this->actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee($this->admin->name);
    $response->assertSee($this->admin->email);
});

it('allows regular user to access profile after authentication', function () {
    $this->actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.profile'));

    $response->assertStatus(200);
    $response->assertSee($this->regularUser->name);
    $response->assertSee($this->regularUser->email);
});

it('prevents profile access after logout', function () {
    // Given: A user is logged in and can access their profile
    $this->actingAs($this->superAdmin);
    $profileResponse = $this->get(route('filament.app.auth.profile'));
    $profileResponse->assertStatus(200);

    // When: The user logs out
    $this->post(route('filament.app.auth.logout'));

    // Then: The user cannot access the profile page anymore
    $redirectedProfileResponse = $this->get(route('filament.app.auth.profile'));
    $redirectedProfileResponse->assertRedirect(route('filament.app.auth.login'));
});
