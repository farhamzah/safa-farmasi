<?php

namespace App\Filament\Resources\PortalApplications\Pages;

use App\Filament\Resources\PortalApplications\PortalApplicationResource;
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
}
