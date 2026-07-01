<?php

namespace App\Filament\Resources\AppCategories;

use App\Filament\Resources\AppCategories\Pages\CreateAppCategory;
use App\Filament\Resources\AppCategories\Pages\EditAppCategory;
use App\Filament\Resources\AppCategories\Pages\ListAppCategories;
use App\Filament\Resources\AppCategories\Schemas\AppCategoryForm;
use App\Filament\Resources\AppCategories\Tables\AppCategoriesTable;
use App\Models\AppCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppCategoryResource extends Resource
{
    protected static ?string $model = AppCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AppCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppCategoriesTable::configure($table);
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
            'index' => ListAppCategories::route('/'),
            'create' => CreateAppCategory::route('/create'),
            'edit' => EditAppCategory::route('/{record}/edit'),
        ];
    }
}
