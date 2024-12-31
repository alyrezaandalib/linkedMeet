<?php

namespace App\Filament\Resources\CompanyActivityTypeResource\Pages;

use App\Filament\Resources\CompanyActivityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyActivityTypes extends ListRecords
{
    protected static string $resource = CompanyActivityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
