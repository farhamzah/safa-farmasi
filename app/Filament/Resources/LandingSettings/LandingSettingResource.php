<?php

namespace App\Filament\Resources\LandingSettings;

use App\Filament\Resources\LandingSettings\Pages\CreateLandingSetting;
use App\Filament\Resources\LandingSettings\Pages\EditLandingSetting;
use App\Filament\Resources\LandingSettings\Pages\ListLandingSettings;
use App\Filament\Resources\LandingSettings\Schemas\LandingSettingForm;
use App\Filament\Resources\LandingSettings\Tables\LandingSettingsTable;
use App\Models\LandingSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LandingSettingResource extends Resource
{
    protected static ?string $model = LandingSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $modelLabel = 'Pengaturan Landing';

    protected static ?string $pluralModelLabel = 'Pengaturan Landing';

    protected static ?string $navigationLabel = 'Pengaturan Landing';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return LandingSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingSettings::route('/'),
            'create' => CreateLandingSetting::route('/create'),
            'edit' => EditLandingSetting::route('/{record}/edit'),
        ];
    }
}
