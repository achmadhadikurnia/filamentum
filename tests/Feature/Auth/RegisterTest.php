<?php

use App\Models\User;
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
});

// ------------------------------------------------------------------------------------------------
// Registration Access Tests
// ------------------------------------------------------------------------------------------------

it('displays the registration page for guests', function () {
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(200);
    $response->assertSee('Register');
    $response->assertSee('Name');
    $response->assertSee('Email');
    $response->assertSee('Password');
});

it('redirects authenticated super admin away from register page', function () {
    $this->actingAs($this->superAdmin);
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated admin away from register page', function () {
    $this->actingAs($this->admin);
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated regular user away from register page', function () {
    $this->actingAs($this->regularUser);
    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302);
    $response->assertRedirect(route('filament.app.pages.dashboard'));
});

// // ------------------------------------------------------------------------------------------------
// // Registration Role Assignment Tests
// // ------------------------------------------------------------------------------------------------

// it('assigns default User role to new registrants', function () {
//     // Given: A user created through the standard registration process
//     // (In a real scenario, this would be done through the registration form,
//     // but we're testing the role assignment logic directly)

//     // When: A new user is created (simulating successful registration)
//     $user = User::create([
//         'name' => 'Role Test User',
//         'email' => 'role@test.com',
//         'password' => bcrypt('password123'),
//     ]);

//     // And: The registration handler assigns the default role
//     $userRole = Role::where('name', 'User')->first();
//     if ($userRole) {
//         $user->assignRole($userRole);
//     }

//     // Then: The user should have the 'User' role assigned
//     expect($user->hasRole('User'))->toBeTrue();
//     expect($user->hasRole('Super Admin'))->toBeFalse();
//     expect($user->hasRole('Admin'))->toBeFalse();
// });
