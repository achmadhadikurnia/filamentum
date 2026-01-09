<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Helpers\TimestampEntries;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('User Information'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Name')),

                        TextEntry::make('email')
                            ->label(__('Email Address')),

                        TextEntry::make('email_verified_at')
                            ->label(__('Email Verified'))
                            ->since()
                            ->tooltip(fn ($record) => $record->email_verified_at?->format('Y-m-d H:i:s'))
                            ->placeholder('-'),

                        TextEntry::make('roles.name')
                            ->label(__('Roles'))
                            ->badge()
                            ->listWithLineBreaks()
                            ->limitList(1)
                            ->expandableLimitedList(),
                    ])
                    ->columnSpanFull(),

                TimestampEntries::section(showDeleted: false),
            ]);
    }
}
