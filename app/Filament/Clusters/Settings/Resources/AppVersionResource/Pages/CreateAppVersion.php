<?php

namespace App\Filament\Clusters\Settings\Resources\AppVersionResource\Pages;

use App\Filament\Clusters\Settings\Resources\AppVersionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAppVersion extends CreateRecord
{
    protected static string $resource = AppVersionResource::class;
}
