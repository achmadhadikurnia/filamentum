<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(__('Full Name'))
                    ->placeholder(__('Enter user\'s full name'))
                    ->helperText(__('The user\'s full name')),
                TextInput::make('email')
                    ->label(__('Email address'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder(__('Enter user\'s email address'))
                    ->helperText(__('Must be a valid email address')),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->minLength(8)
                    ->placeholder(__('Enter password'))
                    ->helperText(__('Password must be at least 8 characters long'))
                    ->visible(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('new_password')
                    ->password()
                    ->label(__('New Password'))
                    ->maxLength(255)
                    ->minLength(8)
                    ->placeholder(__('Enter new password'))
                    ->helperText(__('Leave blank to keep current password. Must be at least 8 characters long'))
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                Select::make('roles')
                    ->multiple()
                    ->preload()
                    ->label(__('Roles'))
                    ->helperText(__('Select one or more roles for this user'))
                    ->options(fn () => static::getAvailableRoles())
                    ->rule('array')
                    ->rule(function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            $availableRoleIds = array_keys(static::getAvailableRoles());

                            if (is_array($value)) {
                                foreach ($value as $roleId) {
                                    if (! in_array($roleId, $availableRoleIds)) {
                                        $fail(__('The selected role is not valid or you do not have permission to assign it.'));

                                        return;
                                    }
                                }
                            }
                        };
                    }),
            ]);
    }

    /**
     * Get available roles based on the current user's permissions.
     * Only show Super Admin role to users who already have that role.
     */
    public static function getAvailableRoles(): array
    {
        // Get the currently authenticated user
        $user = Filament::auth()->user();

        // Get all roles
        $roles = Role::all();

        // If user doesn't have Super Admin role, exclude it from the options
        if (! $user || ! $user->hasRole('Super Admin')) {
            $roles = $roles->filter(fn ($role) => $role->name !== 'Super Admin');
        }

        // Return roles as an associative array
        return $roles->pluck('name', 'id')->toArray();
    }
}
