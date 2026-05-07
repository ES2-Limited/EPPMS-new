<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Models\Department;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Organisation';

    protected static ?string $navigationLabel = 'Unit';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\View::make('filament.components.reference-page-intro')
                ->viewData(['subtitle' => 'Update the Unit Information here', 'stats' => 'org'])
                ->visibleOn(['create', 'edit'])
                ->columnSpanFull(),
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('department_id')
                        ->label('Department Name')
                        ->relationship('department', 'name')
                        ->placeholder('Choose Department')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->label('Unit Name')
                        ->placeholder('Unit Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('function')
                        ->label('Unit Function')
                        ->placeholder('Unit Function')
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
                Tables\Columns\TextColumn::make('department.name')->label('Department Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Unit Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('function')->label('Function')->searchable()->limit(60),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')->label('Department')->options(fn (): array => Department::query()->pluck('name', 'id')->all()),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Are you sure you want to delete this record?')
                        ->modalSubmitActionLabel('Delete')
                        ->modalCancelActionLabel('Cancel')
                        ->before(fn (Unit $record) => $record->forceFill(['deleted_by' => auth()->id()])->saveQuietly()),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'view' => Pages\ViewUnit::route('/{record}'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
