<?php

namespace App\Filament\Resources\LandingSettings\Pages;

use App\Filament\Resources\LandingSettings\LandingSettingResource;
use App\Models\LandingSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateLandingSetting extends CreateRecord
{
    protected static string $resource = LandingSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['type'] ?? null) === 'image' && array_key_exists('value', $data)) {
            $data['value'] = LandingSetting::normalizePublicAssetPathValue($data['value']);
        }

        return $data;
    }
}
