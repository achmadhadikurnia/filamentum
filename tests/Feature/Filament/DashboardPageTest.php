<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Auth\Pages\Login;
use Filament\Pages\Dashboard;
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
// Dashboard Access Tests
// ------------------------------------------------------------------------------------------------

it('redirects unauthenticated users to login page when accessing dashboard', function () {
    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertRedirect(route('filament.app.auth.login'));
});

it('allows super admin to access dashboard after authentication', function () {
    $this->actingAs($this->superAdmin);

    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertSuccessful()
        ->assertSee('Dashboard');
});

it('allows admin to access dashboard after authentication', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertSuccessful()
        ->assertSee('Dashboard');
});

it('allows regular user to access dashboard after authentication', function () {
    $this->actingAs($this->regularUser);

    $response = $this->get(route('filament.app.pages.dashboard'));

    $response->assertSuccessful()
        ->assertSee('Dashboard');
});

// it('redirects unauthenticated users to login page when accessing dashboard', function () {
//     Livewire::test(Dashboard::class)
//         ->assertRedirect(Login::class);
// });

// it('allows super admin to access dashboard after authentication', function () {
//     Livewire::actingAs($this->superAdmin);

//     Livewire::test(Dashboard::class)
//         ->assertSuccessful()
//         ->assertSee('Dashboard');
// });

// it('allows admin to access dashboard after authentication', function () {
//     Livewire::actingAs($this->admin);

//     Livewire::test(Dashboard::class)
//         ->assertSuccessful()
//         ->assertSee('Dashboard');
// });

// it('allows regular user to access dashboard after authentication', function () {
//     Livewire::actingAs($this->regularUser);

//     Livewire::test(Dashboard::class)
//         ->assertSuccessful()
//         ->assertSee('Dashboard');
// });
