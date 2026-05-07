<?php

namespace App\Filament\Resources;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorResource\Pages;
use App\Models\Contractor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class ContractorResource extends Resource
{
    protected static ?string $model = Contractor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Firm';

    protected static ?string $navigationLabel = 'Firms';

    protected static ?int $navigationSort = 1;

    public static function firmTypeOptions(): array
    {
        return [
            Contractor::TYPE_CONTRACTOR => 'Contractor',
            Contractor::TYPE_CONSULTANT => 'Consultant',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('firm_name')
                ->label('Firm Name')
                ->required()
                ->maxLength(255)
                ->validationMessages(['required' => 'Enter the firm name.'])
                ->dehydrated(false),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->rule(fn (?Contractor $record) => Rule::unique('users', 'email')->ignore($record?->user_id))
                ->validationMessages([
                    'required' => 'Enter the firm email address.',
                    'email' => 'Enter a valid firm email address.',
                    'unique' => 'This email address is already in use.',
                ])
                ->dehydrated(false),
            Forms\Components\TextInput::make('phone')->tel()->maxLength(30)->dehydrated(false),
            Forms\Components\Select::make('firm_type_id')->label('Firm Type')->options(static::firmTypeOptions())->required()->native(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Firm Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('user.phone')->label('Phone')->searchable(),
                Tables\Columns\TextColumn::make('firm_type_id')->label('Firm Type')->formatStateUsing(fn (int $state): string => $state === Contractor::TYPE_CONSULTANT ? 'Consultant' : 'Contractor')->badge(),
                Tables\Columns\TextColumn::make('personnel_count')->label('Personnel')->counts('personnel')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('firm_type_id')->label('Firm Type')->options(static::firmTypeOptions()),
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
            ->with(['user', 'personnel.user']);

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole([RoleAndPermissions::CONTRACTOR, RoleAndPermissions::CONSULTANT])) {
            return $query->where('user_id', $user->id);
        }

        if ($user->hasRole(RoleAndPermissions::CONTRACTOR_PERSONNEL)) {
            return $query->whereHas('personnel', fn (Builder $personnel): Builder => $personnel->where('user_id', $user->id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractors::route('/'),
            'create' => Pages\CreateContractor::route('/create'),
            'view' => Pages\ViewContractor::route('/{record}'),
            'edit' => Pages\EditContractor::route('/{record}/edit'),
        ];
    }
}
