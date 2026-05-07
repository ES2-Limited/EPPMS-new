<?php

namespace App\Filament\Resources;

use App\Constants\NigeriaStates;
use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Organisation';

    protected static ?string $navigationLabel = 'Office Location';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\View::make('filament.components.reference-page-intro')
                ->viewData(['subtitle' => 'Create an Office Information here', 'stats' => 'org'])
                ->visibleOn(['create', 'edit'])
                ->columnSpanFull(),
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Office Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')
                        ->label('Type of office Location')
                        ->options([
                            'Regional' => 'Regional',
                            'Headquarter' => 'Headquarter',
                        ])
                        ->required()
                        ->native(false),
                    Forms\Components\Textarea::make('address')
                        ->label('Address')
                        ->placeholder('Address')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('state')
                        ->label('State')
                        ->options(NigeriaStates::states())
                        ->placeholder('-SELECT Office State-')
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('lga', null)),
                    Forms\Components\Select::make('lga')
                        ->label('L.G.A.')
                        ->options(fn (Get $get): array => NigeriaStates::lgas($get('state')))
                        ->placeholder('-SELECT-')
                        ->required()
                        ->searchable()
                        ->native(false),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->placeholder(fn (): string => app_organisation()?->email ?? 'office@example.com')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->placeholder('Phone Number')
                        ->tel()
                        ->minLength(7)
                        ->maxLength(30)
                        ->required(),
                ])
                ->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Office Type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('state')->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state')->options(fn (): array => Office::query()->whereNotNull('state')->pluck('state', 'state')->all()),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Are you sure you want to delete this record?')
                        ->modalSubmitActionLabel('Delete')
                        ->modalCancelActionLabel('Cancel')
                        ->before(fn (Office $record) => $record->forceFill(['deleted_by' => auth()->id()])->saveQuietly()),
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'view' => Pages\ViewOffice::route('/{record}'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
