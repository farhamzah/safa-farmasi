<?php

namespace App\Filament\Resources\PortalApplications\Pages;

use App\Filament\Resources\PortalApplications\PortalApplicationResource;
use App\Models\PortalApplication;
use Filament\Resources\Pages\CreateRecord;

class CreatePortalApplication extends CreateRecord
{
    protected static string $resource = PortalApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (array_key_exists('thumbnail_path', $data)) {
            $data['thumbnail_path'] = PortalApplication::normalizeThumbnailPathValue($data['thumbnail_path']);
        }

        return $data;
    }
}
