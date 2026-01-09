<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Helpers\TimestampColumns;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->copyable(),
                TextColumn::make('email')
                    ->label(__('Email address'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->expandableLimitedList()
                    ->wrap()
                    ->copyable(),
                IconColumn::make('email_verified_at')
                    ->label(__('Verified'))
                    ->boolean()
                    ->tooltip(fn ($record) => $record->email_verified_at?->format('Y-m-d H:i:s'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ...TimestampColumns::columns(showDeleted: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('verify_email')
                        ->label(__('Verify Email'))
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (User $record): bool => ! $record->hasVerifiedEmail())
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $record->markEmailAsVerified()),
                ]),
            ]);
    }
}
