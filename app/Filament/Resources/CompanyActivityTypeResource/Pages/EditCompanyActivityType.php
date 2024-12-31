<?php

namespace App\Filament\Resources\CompanyActivityTypeResource\Pages;

use App\Filament\Resources\CompanyActivityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyActivityType extends EditRecord
{
    protected static string $resource = CompanyActivityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
