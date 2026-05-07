<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectorateResource\Pages;
use App\Models\Directorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DirectorateResource extends Resource
{
    protected static ?string $model = Directorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Organisation';

    protected static ?string $navigationLabel = 'Directorates';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\View::make('filament.components.reference-page-intro')
                ->viewData(['subtitle' => 'Enter the Information to create a directorate', 'stats' => 'org'])
                ->visibleOn(['create', 'edit'])
                ->columnSpanFull(),
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('directorate_code')
                        ->label('Directorate ID')
                        ->placeholder('Directorate ID')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('name')
                        ->label('Directorate Name')
                        ->placeholder('Directorate Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('function')
                        ->label('Directorate Function')
                        ->placeholder('Directorate Function')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('function')->label('Function')->searchable()->limit(60),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Are you sure you want to delete this record?')
                        ->modalSubmitActionLabel('Delete')
                        ->modalCancelActionLabel('Cancel')
                        ->before(fn (Directorate $record) => $record->forceFill(['deleted_by' => auth()->id()])->saveQuietly()),
                ]),
            ])
            ->bulkActions([])
            ->paginated([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDirectorates::route('/'),
            'create' => Pages\CreateDirectorate::route('/create'),
            'view' => Pages\ViewDirectorate::route('/{record}'),
            'edit' => Pages\EditDirectorate::route('/{record}/edit'),
        ];
    }
}
