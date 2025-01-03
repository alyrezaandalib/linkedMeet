<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\AppVersionResource\Pages;
use App\Filament\Clusters\Settings\Resources\AppVersionResource\RelationManagers;
use App\Models\AppVersion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppVersionResource extends Resource
{
    protected static ?string $model = AppVersion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('platform')
                    ->required()
                ->options(['web' => 'Web', 'android' => 'Android', 'ios' => 'iOS'])
                ->default(fn (AppVersion $appVersion) => $appVersion->platform ?? null),
                Forms\Components\TextInput::make('version')
                    ->required(),
                Forms\Components\RichEditor::make('release_notes')->required(),
                Forms\Components\Toggle::make('is_mandatory')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform')->searchable(),
                Tables\Columns\TextColumn::make('version')->searchable(),
                Tables\Columns\IconColumn::make('is_mandatory')->boolean(),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('updated_at'),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_mandatory')->toggle(),
                Tables\Filters\SelectFilter::make('platform')
                    ->options([
                        'web' => 'Web',
                        'android' => 'Android',
                        'ios' => 'iOS',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])->defaultSort('version', 'desc');
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
            'index' => Pages\ListAppVersions::route('/'),
            'create' => Pages\CreateAppVersion::route('/create'),
            'edit' => Pages\EditAppVersion::route('/{record}/edit'),
        ];
    }
}
