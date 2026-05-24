<?php

namespace App\Filament\Resources\PortalApplications\Tables;

use App\Models\PortalApplication;
use App\Services\DuplicatePortalApplication;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PortalApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('categories')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('name'))
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"><rect width="80" height="80" rx="12" fill="%230f766e"/><text x="40" y="48" text-anchor="middle" font-size="20" fill="white" font-family="Arial">SA</text></svg>'),
                TextColumn::make('name')
                    ->label('Aplikasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('short_name')
                    ->label('Singkatan')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('Link')
                    ->limit(32)
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'internal' => 'Internal',
                        'maintenance' => 'Maintenance',
                        'coming_soon' => 'Segera Hadir',
                        default => 'Nonaktif',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'internal' => 'info',
                        'maintenance' => 'warning',
                        'coming_soon' => 'primary',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Utama')
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('updated_at')
                    ->label('Update')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'internal' => 'Internal',
                        'maintenance' => 'Maintenance',
                        'coming_soon' => 'Segera Hadir',
                        'inactive' => 'Nonaktif',
                    ]),
                SelectFilter::make('categories')
                    ->label('Kategori')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->recordActions([
                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (PortalApplication $record): void {
                        app(DuplicatePortalApplication::class)->duplicate($record);

                        Notification::make()
                            ->title('Aplikasi berhasil diduplikasi')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('previewLanding')
                    ->label('Preview Landing')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(url('/'))
                    ->openUrlInNewTab(),
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(route('admin.exports.applications'))
                    ->openUrlInNewTab(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
