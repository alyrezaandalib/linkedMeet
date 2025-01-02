<?php

namespace App\Filament\Resources;

use App\Filament\Imports\CompanyActivityTypeImporter;
use App\Filament\Resources\CompanyActivityTypeResource\Pages;
use App\Filament\Resources\CompanyActivityTypeResource\RelationManagers;
use App\Models\CompanyActivityType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyActivityTypeResource extends Resource
{
    protected static ?string $model = CompanyActivityType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(CompanyActivityTypeImporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyActivityTypes::route('/'),
            'create' => Pages\CreateCompanyActivityType::route('/create'),
            'edit' => Pages\EditCompanyActivityType::route('/{record}/edit'),
        ];
    }
}
