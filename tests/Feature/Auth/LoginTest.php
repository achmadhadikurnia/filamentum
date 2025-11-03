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
// Login Page Access Tests
// ------------------------------------------------------------------------------------------------

it('displays the login page for guests', function () {
    $response = $this->get(route('filament.app.auth.login'));

    $response->assertOk();
    $response->assertSee('login');
    $response->assertSee('email');
    $response->assertSee('password');
});

it('redirects authenticated super admin away from login page', function () {
    Livewire::actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.login'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated admin away from login page', function () {
    Livewire::actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.login'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated regular user away from login page', function () {
    Livewire::actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.login'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

// ------------------------------------------------------------------------------------------------
// Dashboard Access Tests
// ------------------------------------------------------------------------------------------------

it('redirects unauthenticated users to login page when accessing dashboard', function () {
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.auth.login'));
});

it('allows super admin to access dashboard after authentication', function () {
    Livewire::actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});

it('allows admin to access dashboard after authentication', function () {
    Livewire::actingAs($this->admin);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});

it('allows regular user to access dashboard after authentication', function () {
    Livewire::actingAs($this->regularUser);
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});

// ------------------------------------------------------------------------------------------------
// Authentication State Tests
// ------------------------------------------------------------------------------------------------

it('redirects to login after super admin logs out', function () {
    // Given: A user is logged in and can access their dashboard
    Livewire::actingAs($this->superAdmin);
    $dashboardResponse = $this->get(route('filament.app.pages.dashboard'));
    $dashboardResponse->assertStatus(200);

    // When: The user logs out
    $logoutResponse = $this->post(route('filament.app.auth.logout'));
    $logoutResponse->assertStatus(302);
    $this->assertGuest();

    // Then: The user is redirected to login when trying to access dashboard again
    $redirectedDashboardResponse = $this->get(route('filament.app.pages.dashboard'));
    $redirectedDashboardResponse->assertStatus(302);
    $redirectedDashboardResponse->assertRedirect(route('filament.app.auth.login'));
});

it('redirects to login after admin logs out', function () {
    // Given: A user is logged in and can access their dashboard
    Livewire::actingAs($this->admin);
    $dashboardResponse = $this->get(route('filament.app.pages.dashboard'));
    $dashboardResponse->assertStatus(200);

    // When: The user logs out
    $logoutResponse = $this->post(route('filament.app.auth.logout'));
    $logoutResponse->assertStatus(302);
    $this->assertGuest();

    // Then: The user is redirected to login when trying to access dashboard again
    $redirectedDashboardResponse = $this->get(route('filament.app.pages.dashboard'));
    $redirectedDashboardResponse->assertStatus(302);
    $redirectedDashboardResponse->assertRedirect(route('filament.app.auth.login'));
});

it('redirects to login after regular user logs out', function () {
    // Given: A user is logged in and can access their dashboard
    Livewire::actingAs($this->regularUser);
    $dashboardResponse = $this->get(route('filament.app.pages.dashboard'));
    $dashboardResponse->assertStatus(200);

    // When: The user logs out
    $logoutResponse = $this->post(route('filament.app.auth.logout'));
    $logoutResponse->assertStatus(302);
    $this->assertGuest();

    // Then: The user is redirected to login when trying to access dashboard again
    $redirectedDashboardResponse = $this->get(route('filament.app.pages.dashboard'));
    $redirectedDashboardResponse->assertStatus(302);
    $redirectedDashboardResponse->assertRedirect(route('filament.app.auth.login'));
});
