<?php

namespace App\Filament\Helpers;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Helper class for reusable timestamp entries in Filament infolists.
 */
class TimestampEntries
{
    /**
     * Create a timestamps section with created_at, updated_at, and optionally deleted_at.
     *
     * @param  bool  $showDeleted  Whether to show deleted_at (for models with SoftDeletes)
     */
    public static function section(bool $showDeleted = true): Section
    {
        return Section::make(__('Timestamps'))
            ->schema(static::entries($showDeleted))
            ->columns(3)
            ->columnSpanFull();
    }

    /**
     * Create timestamp entries array.
     *
     * @param  bool  $showDeleted  Whether to include deleted_at entry
     */
    public static function entries(bool $showDeleted = true): array
    {
        $entries = [
            TextEntry::make('created_at')
                ->label(__('Created'))
                ->since()
                ->dateTimeTooltip()
                ->placeholder('-'),

            TextEntry::make('updated_at')
                ->label(__('Updated'))
                ->since()
                ->dateTimeTooltip()
                ->placeholder('-'),
        ];

        if ($showDeleted) {
            $entries[] = TextEntry::make('deleted_at')
                ->label(__('Deleted'))
                ->since()
                ->dateTimeTooltip()
                ->visible(fn (Model $record): bool => method_exists($record, 'trashed') && $record->trashed());
        }

        return $entries;
    }
}
