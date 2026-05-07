<?php

namespace App\Filament\Resources;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorPersonnelResource\Pages;
use App\Models\Contractor;
use App\Models\ContractorPersonnel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class ContractorPersonnelResource extends Resource
{
    protected static ?string $model = ContractorPersonnel::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Firm';

    protected static ?string $navigationLabel = 'Contractor Personnel';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('contractor_id')
                ->label('Firm')
                ->options(fn (): array => Contractor::query()->with('user')->get()->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Firm #'.$contractor->id])->all())
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->rule(fn (?ContractorPersonnel $record) => Rule::unique('users', 'email')->ignore($record?->user_id))
                ->validationMessages([
                    'required' => 'Enter the personnel email address.',
                    'email' => 'Enter a valid personnel email address.',
                    'unique' => 'This email address is already in use.',
                ]),
            Forms\Components\TextInput::make('phone')->tel()->maxLength(30),
            Forms\Components\TextInput::make('position')->maxLength(255),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contractor.user.name')->label('Firm Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('position')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contractor_id')
                    ->label('Firm')
                    ->options(fn (): array => Contractor::query()->with('user')->get()->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Firm #'.$contractor->id])->all()),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['user', 'contractor.user']);

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole([RoleAndPermissions::CONTRACTOR, RoleAndPermissions::CONSULTANT])) {
            return $query->whereHas('contractor', fn (Builder $contractor): Builder => $contractor->where('user_id', $user->id));
        }

        if ($user->hasRole(RoleAndPermissions::CONTRACTOR_PERSONNEL)) {
            return $query->whereIn('contractor_id', ContractorPersonnel::query()->where('user_id', $user->id)->select('contractor_id'));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractorPersonnel::route('/'),
            'create' => Pages\CreateContractorPersonnel::route('/create'),
            'view' => Pages\ViewContractorPersonnel::route('/{record}'),
            'edit' => Pages\EditContractorPersonnel::route('/{record}/edit'),
        ];
    }
}
