<?php

namespace App\Filament\Imports;

use App\Models\CompanyActivityType;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CompanyActivityTypeImporter extends Importer
{
    protected static ?string $model = CompanyActivityType::class;

    public static function getColumns(): array
    {
        return [
            //
        ];
    }

    public function resolveRecord(): ?CompanyActivityTypeResource
    {
        return CompanyActivityTypeResource::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your company activity type resource import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
