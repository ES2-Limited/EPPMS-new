<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use App\Models\Directorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'Organisation';

    protected static ?string $navigationLabel = 'Department';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\View::make('filament.components.reference-page-intro')
                ->viewData(['subtitle' => 'Enter information to create department', 'stats' => 'org'])
                ->visibleOn(['create', 'edit'])
                ->columnSpanFull(),
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('directorate_id')
                        ->label('Directorate Name')
                        ->relationship('directorate', 'name')
                        ->placeholder('Choose Directorate')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('department_code')
                        ->label('Department ID')
                        ->placeholder('Department ID')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('name')
                        ->label('Department Name')
                        ->placeholder('Department Name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('function')
                        ->label('Department Function')
                        ->placeholder('Department Function')
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
                Tables\Columns\TextColumn::make('directorate.name')->label('Directorate Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Department Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('function')->label('Function')->searchable()->limit(60),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('directorate_id')->label('Directorate')->options(fn (): array => Directorate::query()->pluck('name', 'id')->all()),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Are you sure you want to delete this record?')
                        ->modalSubmitActionLabel('Delete')
                        ->modalCancelActionLabel('Cancel')
                        ->before(fn (Department $record) => $record->forceFill(['deleted_by' => auth()->id()])->saveQuietly()),
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
