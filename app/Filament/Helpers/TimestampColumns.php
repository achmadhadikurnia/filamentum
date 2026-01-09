<?php

namespace App\Filament\Helpers;

use Filament\Tables\Columns\TextColumn;

/**
 * Helper class for reusable timestamp columns in Filament tables.
 */
class TimestampColumns
{
    /**
     * Create timestamp columns array for tables.
     *
     * @param  bool  $showDeleted  Whether to include deleted_at column (for models with SoftDeletes)
     * @return array<TextColumn>
     */
    public static function columns(bool $showDeleted = true): array
    {
        $columns = [
            TextColumn::make('created_at')
                ->label(__('Created'))
                ->since()
                ->dateTimeTooltip()
                ->sortable()
                ->wrap()
                ->copyable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->label(__('Updated'))
                ->since()
                ->dateTimeTooltip()
                ->sortable()
                ->wrap()
                ->copyable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];

        if ($showDeleted) {
            $columns[] = TextColumn::make('deleted_at')
                ->label(__('Deleted'))
                ->since()
                ->dateTimeTooltip()
                ->sortable()
                ->wrap()
                ->copyable()
                ->toggleable(isToggledHiddenByDefault: true);
        }

        return $columns;
    }
}
