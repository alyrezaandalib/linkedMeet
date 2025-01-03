<?php

namespace App\Filament\Clusters\Settings\Resources\AppVersionResource\Pages;

use App\Filament\Clusters\Settings\Resources\AppVersionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppVersion extends EditRecord
{
    protected static string $resource = AppVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
