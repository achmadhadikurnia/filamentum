<?php

namespace App\Filament\Resources\Users\Tables;

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
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->copyable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->expandableLimitedList()
                    ->wrap()
                    ->copyable(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->tooltip(fn ($record) => $record->email_verified_at?->format('Y-m-d H:i:s'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable()
                    ->wrap()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable()
                    ->wrap()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        ->label('Verify Email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (User $record): bool => $record->email_verified_at === null)
                        ->requiresConfirmation()
                        ->action(function (User $record): void {
                            $record->email_verified_at = now();
                            $record->save();
                        }),
                ])
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->tooltip('Actions'),
            ]);
    }
}
