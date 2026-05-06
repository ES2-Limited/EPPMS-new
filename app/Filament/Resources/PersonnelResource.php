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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonnelResource extends Resource
{
    protected static ?string $model = Personnel::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Organisation';

    protected static ?string $pluralModelLabel = 'Personnels';

    protected static ?int $navigationSort = 5;

    public static function systemRoleOptions(): array
    {
        return collect(RoleAndPermissions::SYSTEM_ROLES)
            ->mapWithKeys(fn (string $role): array => [$role => str($role)->replace('_', ' ')->title()->toString()])
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('User Details')
                ->schema([
                    Forms\Components\TextInput::make('first_name')->required()->maxLength(255)->visibleOn('create')->dehydrated(fn (string $operation): bool => $operation === 'create'),
                    Forms\Components\TextInput::make('last_name')->required()->maxLength(255)->visibleOn('create')->dehydrated(fn (string $operation): bool => $operation === 'create'),
                    Forms\Components\TextInput::make('email')->email()->required()->unique('users', 'email')->maxLength(255)->visibleOn('create')->dehydrated(fn (string $operation): bool => $operation === 'create'),
                    Forms\Components\TextInput::make('phone')->tel()->maxLength(255)->visibleOn('create')->dehydrated(fn (string $operation): bool => $operation === 'create'),
                    Forms\Components\Select::make('role')->options(static::systemRoleOptions())->required()->searchable()->visibleOn('create')->dehydrated(fn (string $operation): bool => $operation === 'create'),
                    Forms\Components\Placeholder::make('user.name')->label('Name')->content(fn (?Personnel $record): string => $record?->user?->name ?? '-')->visibleOn(['edit', 'view']),
                    Forms\Components\Placeholder::make('user.email')->label('Email')->content(fn (?Personnel $record): string => $record?->user?->email ?? '-')->visibleOn(['edit', 'view']),
                    Forms\Components\Placeholder::make('user.phone')->label('Phone')->content(fn (?Personnel $record): string => $record?->user?->phone ?? '-')->visibleOn(['edit', 'view']),
                ])
                ->columns(2),
            Forms\Components\Section::make('Organisation Assignment')
                ->schema([
                    Forms\Components\Select::make('directorate_id')->relationship('directorate', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('office_id')->relationship('office', 'name')->searchable()->preload(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('user.phone')->label('Phone')->searchable(),
                Tables\Columns\TextColumn::make('user.roles.name')->label('Roles')->badge(),
                Tables\Columns\TextColumn::make('directorate.name')->label('Directorate')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('department.name')->label('Department')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('office.name')->label('Office')->searchable()->sortable(),
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
