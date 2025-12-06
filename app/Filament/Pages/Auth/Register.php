<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Register extends BaseRegister
{
    use WithRateLimiting;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        // Assign default role from filament-shield config
        $defaultRoleName = config('filament-shield.panel_user.name');
        if ($defaultRoleName) {
            $userRole = Role::where('name', $defaultRoleName)->first();
            if ($userRole) {
                $user->assignRole($userRole);
            }
        }

        return $user;
    }
}
