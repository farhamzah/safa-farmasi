<?php

namespace App\Filament\Resources\PortalApplications;

use App\Filament\Resources\PortalApplications\Pages\CreatePortalApplication;
use App\Filament\Resources\PortalApplications\Pages\EditPortalApplication;
use App\Filament\Resources\PortalApplications\Pages\ListPortalApplications;
use App\Filament\Resources\PortalApplications\Schemas\PortalApplicationForm;
use App\Filament\Resources\PortalApplications\Tables\PortalApplicationsTable;
use App\Models\PortalApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortalApplicationResource extends Resource
{
    protected static ?string $model = PortalApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Aplikasi';

    protected static ?string $pluralModelLabel = 'Aplikasi';

    protected static ?string $navigationLabel = 'Aplikasi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PortalApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortalApplicationsTable::configure($table);
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
            'index' => ListPortalApplications::route('/'),
            'create' => CreatePortalApplication::route('/create'),
            'edit' => EditPortalApplication::route('/{record}/edit'),
        ];
    }
}
