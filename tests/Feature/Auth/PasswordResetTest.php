<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;

beforeEach(function () {
    // Seed the database with roles, permissions, and users
    $this->seed(ShieldSeeder::class);
    $this->seed(RoleUserSeeder::class);
});

// ------------------------------------------------------------------------------------------------
// Password Reset Access Tests
// ------------------------------------------------------------------------------------------------

it('displays password reset request page', function () {
    $response = $this->get(route('filament.app.auth.password-reset.request'));

    $response->assertStatus(200);
    $response->assertSee('password');
    $response->assertSee('email');
});

it('has password reset form page', function () {
    $resetRoute = route('filament.app.auth.password-reset.reset', ['token' => 'test-token']);

    expect($resetRoute)->toBeString();
    expect($resetRoute)->toContain('password-reset');
});

// ------------------------------------------------------------------------------------------------
// Password Reset Route Tests
// ------------------------------------------------------------------------------------------------

it('has working password reset routes', function () {
    $requestRoute = route('filament.app.auth.password-reset.request');
    $resetRoute = route('filament.app.auth.password-reset.reset', ['token' => 'test-token']);

    expect($requestRoute)->toBeString();
    expect($resetRoute)->toBeString();
    expect($requestRoute)->toContain('password-reset');
    expect($resetRoute)->toContain('password-reset');
});
