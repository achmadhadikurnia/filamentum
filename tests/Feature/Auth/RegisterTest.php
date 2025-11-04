<?php

use App\Filament\Pages\Auth\Register;
use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Livewire\Livewire;

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
// Registration Page Access Tests
// ------------------------------------------------------------------------------------------------

it('displays the registration page for guests when enabled', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    $response = $this->get(route('filament.app.auth.register'));

    $response->assertSuccessful()
        ->assertSee('Register');
});

it('redirects authenticated super admin away from registration page', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    Livewire::actingAs($this->superAdmin);

    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302)
        ->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated admin away from registration page', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    Livewire::actingAs($this->admin);

    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302)
        ->assertRedirect(route('filament.app.pages.dashboard'));
});

it('redirects authenticated regular user away from registration page', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    Livewire::actingAs($this->regularUser);

    $response = $this->get(route('filament.app.auth.register'));

    $response->assertStatus(302)
        ->assertRedirect(route('filament.app.pages.dashboard'));
});

// ------------------------------------------------------------------------------------------------
// Registration Feature Disabled Tests
// ------------------------------------------------------------------------------------------------

it('blocks registration functionality when disabled', function () {
    // Ensure registration is disabled
    config(['filamentum.features.registration' => false]);

    $response = $this->get(route('filament.app.auth.register'));

    // When registration is disabled, it still shows the page but registration won't work
    $response->assertSuccessful();
});

// ------------------------------------------------------------------------------------------------
// User Registration Tests
// ------------------------------------------------------------------------------------------------

it('allows valid user registration when enabled', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    Livewire::test(Register::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'passwordConfirmation' => 'password123', // Note the camelCase
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    // Verify the user was created in the database
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    // Verify the user was assigned the default 'User' role
    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('User'))->toBeTrue();
});

it('validates required fields during registration', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    Livewire::test(Register::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => '',
            'email' => '',
            'password' => '',
            'passwordConfirmation' => '',
        ])
        ->call('register')
        ->assertHasFormErrors(['name', 'email', 'password']);
});

it('validates email format during registration', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    Livewire::test(Register::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'passwordConfirmation' => 'password123',
        ])
        ->call('register')
        ->assertHasFormErrors(['email']);
});

it('validates password confirmation during registration', function () {
    // Temporarily enable registration for this test
    config(['filamentum.features.registration' => true]);

    Livewire::test(Register::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'passwordConfirmation' => 'different-password',
        ])
        ->call('register')
        ->assertHasFormErrors(['password']);
});
