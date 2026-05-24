<?php

namespace App\Filament\Resources\PortalApplications\Pages;

use App\Filament\Resources\PortalApplications\PortalApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortalApplications extends ListRecords
{
    protected static string $resource = PortalApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
