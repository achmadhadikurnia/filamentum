<?php

use App\Models\User;
use Database\Seeders\RoleUserSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Auth\Notifications\VerifyEmailChange;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

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
// Email Change Verification Notification Tests
// ------------------------------------------------------------------------------------------------

it('sends email change verification notifications for super admin', function () {
    Notification::fake();

    // Create a user
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Generate block verification URL
    $blockVerificationUrl = 'http://example.com/block';

    // Send email change notification
    $user->notify(new NoticeOfEmailChangeRequest('newemail@example.com', $blockVerificationUrl));

    Notification::assertSentTo($user, NoticeOfEmailChangeRequest::class);
});

it('sends email change verification notifications for admin', function () {
    Notification::fake();

    // Create a user with admin role
    $user = User::factory()->create([
        'email' => 'admin@example.com',
    ]);
    $user->assignRole('Admin');

    // Generate block verification URL
    $blockVerificationUrl = 'http://example.com/block';

    // Send email change notification
    $user->notify(new NoticeOfEmailChangeRequest('newemail@example.com', $blockVerificationUrl));

    Notification::assertSentTo($user, NoticeOfEmailChangeRequest::class);
});

it('sends email change verification notifications for regular user', function () {
    Notification::fake();

    // Create a user with regular user role
    $user = User::factory()->create([
        'email' => 'user@example.com',
    ]);
    $user->assignRole('User');

    // Generate block verification URL
    $blockVerificationUrl = 'http://example.com/block';

    // Send email change notification
    $user->notify(new NoticeOfEmailChangeRequest('newemail@example.com', $blockVerificationUrl));

    Notification::assertSentTo($user, NoticeOfEmailChangeRequest::class);
});

it('sends verify email change notification', function () {
    Notification::fake();

    // Create a user
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Send verify email change notification
    $user->notify(new VerifyEmailChange);

    Notification::assertSentTo($user, VerifyEmailChange::class);
});
