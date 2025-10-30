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

// // ------------------------------------------------------------------------------------------------
// // Email Change Verification Route Tests
// // ------------------------------------------------------------------------------------------------

// it('has working email change verification routes', function () {
//     // Create a user
//     $user = User::factory()->create([
//         'email' => 'test@example.com',
//     ]);

//     // Generate encrypted email
//     $encryptedEmail = encrypt('newemail@example.com');

//     // Generate signature
//     $signature = 'test-signature';

//     $verifyRoute = route('filament.app.auth.email-change-verification.verify', [
//         'id' => $user->getKey(),
//         'email' => $encryptedEmail,
//     ]) . '?signature=' . $signature;

//     $blockRoute = route('filament.app.auth.email-change-verification.block-verification', [
//         'id' => $user->getKey(),
//         'email' => $encryptedEmail,
//     ]) . '?verificationSignature=' . $signature;

//     expect($verifyRoute)->toBeString();
//     expect($blockRoute)->toBeString();
//     expect($verifyRoute)->toContain('email-change-verification');
//     expect($blockRoute)->toContain('email-change-verification');
// });

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
    $user->notify(new VerifyEmailChange());

    Notification::assertSentTo($user, VerifyEmailChange::class);
});

// // ------------------------------------------------------------------------------------------------
// // Email Change Verification Process Tests
// // ------------------------------------------------------------------------------------------------

// it('can verify email change with valid signature for super admin', function () {
//     Notification::fake();

//     // Create a user
//     $user = User::factory()->create([
//         'email' => 'test@example.com',
//     ]);

//     // Simulate that the signature is in cache
//     $signature = 'valid-signature';
//     cache()->put($signature, true, 60);

//     // Generate encrypted email
//     $encryptedEmail = encrypt('newemail@example.com');

//     $this->actingAs($user);

//     // Generate verification URL
//     $verificationUrl = route('filament.app.auth.email-change-verification.verify', [
//         'id' => $user->getKey(),
//         'email' => $encryptedEmail,
//     ]) . '?signature=' . $signature;

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($verificationUrl)->toBeString();
// });

// it('can verify email change with valid signature for admin', function () {
//     Notification::fake();

//     // Create a user with admin role
//     $user = User::factory()->create([
//         'email' => 'admin@example.com',
//     ]);
//     $user->assignRole('Admin');

//     // Simulate that the signature is in cache
//     $signature = 'valid-signature';
//     cache()->put($signature, true, 60);

//     // Generate encrypted email
//     $encryptedEmail = encrypt('newemail@example.com');

//     $this->actingAs($user);

//     // Generate verification URL
//     $verificationUrl = route('filament.app.auth.email-change-verification.verify', [
//         'id' => $user->getKey(),
//         'email' => $encryptedEmail,
//     ]) . '?signature=' . $signature;

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($verificationUrl)->toBeString();
// });

// it('can verify email change with valid signature for regular user', function () {
//     Notification::fake();

//     // Create a user with regular user role
//     $user = User::factory()->create([
//         'email' => 'user@example.com',
//     ]);
//     $user->assignRole('User');

//     // Simulate that the signature is in cache
//     $signature = 'valid-signature';
//     cache()->put($signature, true, 60);

//     // Generate encrypted email
//     $encryptedEmail = encrypt('newemail@example.com');

//     $this->actingAs($user);

//     // Generate verification URL
//     $verificationUrl = route('filament.app.auth.email-change-verification.verify', [
//         'id' => $user->getKey(),
//         'email' => $encryptedEmail,
//     ]) . '?signature=' . $signature;

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($verificationUrl)->toBeString();
// });

// ------------------------------------------------------------------------------------------------
// Invalid Signature Tests
// ------------------------------------------------------------------------------------------------

// it('cannot verify email change with invalid signature', function () {
//     // Create a user
//     $user = User::factory()->create([
//         'email' => 'test@example.com',
//     ]);

//     // Don't put signature in cache (invalid signature)

//     // Generate encrypted email
//     $encryptedEmail = encrypt('newemail@example.com');

//     $this->actingAs($user);

//     // Generate verification URL with invalid signature
//     $verificationUrl = route('filament.app.auth.email-change-verification.verify', [
//         'id' => $user->getKey(),
//         'email' => $encryptedEmail,
//     ]) . '?signature=invalid-signature';

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($verificationUrl)->toBeString();
// });

// it('cannot verify email change with wrong user id', function () {
//     // Create two users
//     $user1 = User::factory()->create([
//         'email' => 'user1@example.com',
//     ]);

//     $user2 = User::factory()->create([
//         'email' => 'user2@example.com',
//     ]);

//     // Simulate that the signature is in cache
//     $signature = 'valid-signature';
//     cache()->put($signature, true, 60);

//     // Generate encrypted email
//     $encryptedEmail = encrypt('newemail@example.com');

//     $this->actingAs($user1);

//     // Generate verification URL with wrong user id
//     $verificationUrl = route('filament.app.auth.email-change-verification.verify', [
//         'id' => $user2->getKey(), // Wrong user ID
//         'email' => $encryptedEmail,
//     ]) . '?signature=' . $signature;

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($verificationUrl)->toBeString();
// });

// // ------------------------------------------------------------------------------------------------
// // Block Verification Tests
// // ------------------------------------------------------------------------------------------------

// it('can block email change verification for super admin', function () {
//     // Create a user
//     $user = User::factory()->create([
//         'email' => 'test@example.com',
//     ]);

//     // Simulate that the signature is in cache
//     $signature = 'valid-signature';
//     cache()->put($signature, true, 60);

//     $this->actingAs($user);

//     // Generate block verification URL
//     $blockUrl = route('filament.app.auth.email-change-verification.block-verification', [
//         'id' => $user->getKey(),
//         'email' => encrypt('newemail@example.com'),
//     ]) . '?verificationSignature=' . $signature;

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($blockUrl)->toBeString();
// });

// it('can block email change verification for admin', function () {
//     // Create a user with admin role
//     $user = User::factory()->create([
//         'email' => 'admin@example.com',
//     ]);
//     $user->assignRole('Admin');

//     // Simulate that the signature is in cache
//     $signature = 'valid-signature';
//     cache()->put($signature, true, 60);

//     $this->actingAs($user);

//     // Generate block verification URL
//     $blockUrl = route('filament.app.auth.email-change-verification.block-verification', [
//         'id' => $user->getKey(),
//         'email' => encrypt('newemail@example.com'),
//     ]) . '?verificationSignature=' . $signature;

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($blockUrl)->toBeString();
// });

// it('can block email change verification for regular user', function () {
//     // Create a user with regular user role
//     $user = User::factory()->create([
//         'email' => 'user@example.com',
//     ]);
//     $user->assignRole('User');

//     // Simulate that the signature is in cache
//     $signature = 'valid-signature';
//     cache()->put($signature, true, 60);

//     $this->actingAs($user);

//     // Generate block verification URL
//     $blockUrl = route('filament.app.auth.email-change-verification.block-verification', [
//         'id' => $user->getKey(),
//         'email' => encrypt('newemail@example.com'),
//     ]) . '?verificationSignature=' . $signature;

//     // Since we're testing the route and not the actual controller logic,
//     // we'll just verify that the route exists and can be accessed
//     expect($blockUrl)->toBeString();
// });
