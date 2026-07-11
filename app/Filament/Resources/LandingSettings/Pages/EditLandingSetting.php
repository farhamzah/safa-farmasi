<?php

namespace App\Filament\Resources\LandingSettings\Pages;

use App\Filament\Resources\LandingSettings\LandingSettingResource;
use App\Models\LandingSetting;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLandingSetting extends EditRecord
{
    protected static string $resource = LandingSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['type'] ?? null) === 'image' && array_key_exists('value', $data)) {
            $data['value'] = LandingSetting::normalizePublicAssetPathValue($data['value']);
        }

        return $data;
    }
}
