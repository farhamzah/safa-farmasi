<?php

namespace App\Filament\Resources\PortalApplications\Pages;

use App\Filament\Resources\PortalApplications\PortalApplicationResource;
use App\Models\PortalApplication;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortalApplication extends EditRecord
{
    protected static string $resource = PortalApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('thumbnail_path', $data)) {
            $data['thumbnail_path'] = PortalApplication::normalizeThumbnailPathValue($data['thumbnail_path']);
        }

        return $data;
    }
}
