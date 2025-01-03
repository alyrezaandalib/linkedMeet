<?php

namespace App\Filament\Clusters\Settings\Resources\AppVersionResource\Pages;

use App\Filament\Clusters\Settings\Resources\AppVersionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppVersions extends ListRecords
{
    protected static string $resource = AppVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
