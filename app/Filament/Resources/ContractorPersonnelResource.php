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
            Forms\Components\View::make('filament.components.reference-page-intro')
                ->viewData(['heading' => 'Personnel Information', 'subtitle' => 'Enter the Personnel Information here', 'stats' => 'firm'])
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
                                ->rule(fn (?ContractorPersonnel $record) => Rule::unique('users', 'email')->ignore($record?->user_id))
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
                    Forms\Components\Grid::make(['default' => 1, 'md' => 2])
                        ->schema([
                            Forms\Components\Select::make('contractor_id')
                                ->label('Project Contractor')
                                ->options(fn (): array => Contractor::query()
                                    ->where('firm_type_id', Contractor::TYPE_CONTRACTOR)
                                    ->with('user')
                                    ->get()
                                    ->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Firm #'.$contractor->id])
                                    ->all())
                                ->placeholder('Choose Contractor')
                                ->searchable()
                                ->preload()
                                ->required(),
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
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contractor.user.name')->label('Firm Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('designation')->label('Designation')->state(fn (ContractorPersonnel $record): ?string => $record->designation ?: $record->position)->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contractor_id')
                    ->label('Firm')
                    ->options(fn (): array => Contractor::query()->where('firm_type_id', Contractor::TYPE_CONTRACTOR)->with('user')->get()->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Firm #'.$contractor->id])->all()),
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
