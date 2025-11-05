<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Auth\Pages\EditProfile;
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
// Profile Page Access Tests
// ------------------------------------------------------------------------------------------------

it('redirects unauthenticated users to login when accessing profile', function () {
    $this->get(EditProfile::getUrl())
        ->assertRedirect(route('filament.app.auth.login'));
});

it('displays the profile page for super admin', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(EditProfile::class)
        ->assertSuccessful()
        ->assertSee('Profile');
});

it('displays the profile page for admin', function () {
    Livewire::actingAs($this->admin);

    Livewire::test(EditProfile::class)
        ->assertSuccessful()
        ->assertSee('Profile');
});

it('displays the profile page for regular user', function () {
    Livewire::actingAs($this->regularUser);

    Livewire::test(EditProfile::class)
        ->assertSuccessful()
        ->assertSee('Profile');
});

// ------------------------------------------------------------------------------------------------
// Profile Validation Tests
// ------------------------------------------------------------------------------------------------

it('validates required name field during profile update', function () {
    Livewire::actingAs($this->superAdmin);

    Livewire::test(EditProfile::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => '',
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasFormErrors(['name']);
});

// ------------------------------------------------------------------------------------------------
// Profile Update Tests
// ------------------------------------------------------------------------------------------------

it('allows super admin to update their profile name', function () {
    Livewire::actingAs($this->superAdmin);

    $newName = 'Updated Super Admin';

    Livewire::test(EditProfile::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => $newName,
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSuccessful();

    // Verify the user name was updated in the database
    $this->assertDatabaseHas('users', [
        'id' => $this->superAdmin->id,
        'name' => $newName,
    ]);
});

it('allows admin to update their profile name', function () {
    Livewire::actingAs($this->admin);

    $newName = 'Updated Admin';

    Livewire::test(EditProfile::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => $newName,
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSuccessful();

    // Verify the user name was updated in the database
    $this->assertDatabaseHas('users', [
        'id' => $this->admin->id,
        'name' => $newName,
    ]);
});

it('allows regular user to update their profile name', function () {
    Livewire::actingAs($this->regularUser);

    $newName = 'Updated User';

    Livewire::test(EditProfile::class)
        ->assertSuccessful()
        ->fillForm([
            'name' => $newName,
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSuccessful();

    // Verify the user name was updated in the database
    $this->assertDatabaseHas('users', [
        'id' => $this->regularUser->id,
        'name' => $newName,
    ]);
});
