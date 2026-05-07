<?php

namespace App\Filament\Resources;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\PersonnelResource\Pages;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Personnel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class PersonnelResource extends Resource
{
    protected static ?string $model = Personnel::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Organisation';

    protected static ?string $navigationLabel = 'Personnels';

    protected static ?string $pluralModelLabel = 'Personnels';

    protected static ?int $navigationSort = 6;

    public static function systemRoleOptions(): array
    {
        return collect(RoleAndPermissions::SYSTEM_ROLES)
            ->mapWithKeys(fn (string $role): array => [$role => str($role)->replace('_', ' ')->title()->toString()])
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\View::make('filament.components.reference-page-intro')
                ->viewData(['subtitle' => 'Enter Information to create a Personnel account', 'stats' => 'org'])
                ->visibleOn(['create', 'edit'])
                ->columnSpanFull(),
            Forms\Components\Section::make('Personnel Credentials')
                ->schema([
                    Forms\Components\Grid::make(['default' => 1, 'md' => 3])
                        ->schema([
                            Forms\Components\TextInput::make('first_name')
                                ->label('First Name')
                                ->placeholder('First Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('last_name')
                                ->label('Last Name')
                                ->placeholder('Last Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('other_name')
                                ->label('Other')
                                ->placeholder('Other')
                                ->maxLength(255),
                        ]),
                    Forms\Components\Grid::make(['default' => 1, 'md' => 3])
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->placeholder('Johndoe@gmail.com')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->rule(fn (?Personnel $record) => Rule::unique('users', 'email')->ignore($record?->user_id))
                                ->validationMessages([
                                    'required' => 'Enter the personnel email address.',
                                    'email' => 'Enter a valid personnel email address.',
                                    'unique' => 'This email address is already in use.',
                                ]),
                            Forms\Components\TextInput::make('phone')
                                ->label('Phone Number')
                                ->placeholder('Phone Number')
                                ->tel()
                                ->minLength(7)
                                ->maxLength(30)
                                ->required(),
                            Forms\Components\TextInput::make('designation')
                                ->label('Designation')
                                ->placeholder('Designation')
                                ->maxLength(255),
                        ]),
                    Forms\Components\Grid::make(['default' => 1, 'md' => 3])
                        ->schema([
                            Forms\Components\Select::make('role')
                                ->label('Personnel Role')
                                ->options(static::systemRoleOptions())
                                ->placeholder('Choose Role')
                                ->required()
                                ->searchable()
                                ->native(false),
                            Forms\Components\Select::make('directorate_id')
                                ->label('Directorate Name')
                                ->options(fn (): array => Directorate::query()->pluck('name', 'id')->all())
                                ->placeholder('Choose Directorate')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('department_id', null)),
                            Forms\Components\Select::make('department_id')
                                ->label('Department')
                                ->options(fn (Get $get): array => Department::query()
                                    ->when($get('directorate_id'), fn (Builder $query, $directorateId): Builder => $query->where('directorate_id', $directorateId))
                                    ->pluck('name', 'id')
                                    ->all())
                                ->placeholder('Choose Department')
                                ->searchable()
                                ->preload(),
                        ]),
                    Forms\Components\Grid::make(['default' => 1, 'md' => 2])
                        ->schema([
                            Forms\Components\Select::make('office_id')
                                ->label('Office Location')
                                ->options(fn (): array => Office::query()->pluck('name', 'id')->all())
                                ->placeholder('Select an office')
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->visibleOn(['create', 'edit'])
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->dehydrated(fn (?string $state): bool => filled($state)),
                        ]),
                ])
                ->columns(1),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone')->state(fn (Personnel $record): ?string => $record->phone ?: $record->user?->phone)->searchable(),
                Tables\Columns\TextColumn::make('designation')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.roles.name')->label('Roles')->badge(),
                Tables\Columns\TextColumn::make('directorate.name')->label('Directorate')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('department.name')->label('Department')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('office.name')->label('Office')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('directorate_id')->label('Directorate')->options(fn (): array => Directorate::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('department_id')->label('Department')->options(fn (): array => Department::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('office_id')->label('Office')->options(fn (): array => Office::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('role')
                    ->options(static::systemRoleOptions())
                    ->query(fn (Builder $query, array $data): Builder => blank($data['value'] ?? null) ? $query : $query->whereHas('user.roles', fn (Builder $roles): Builder => $roles->where('name', $data['value']))),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['user.roles', 'directorate', 'department', 'office']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonnel::route('/'),
            'create' => Pages\CreatePersonnel::route('/create'),
            'view' => Pages\ViewPersonnel::route('/{record}'),
            'edit' => Pages\EditPersonnel::route('/{record}/edit'),
        ];
    }
}
