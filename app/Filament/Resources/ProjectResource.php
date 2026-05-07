<?php

namespace App\Filament\Resources;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Project;
use App\Support\ProjectAccess;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Project';

    protected static ?string $navigationLabel = 'Projects';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Project Details')
                ->schema([
                    // Row 1 – full width
                    Forms\Components\TextInput::make('name')
                        ->label('Project Name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    // Row 2 – full width
                    Forms\Components\Textarea::make('description')
                        ->label('Project Description')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    // Row 3 – 2 columns (each span 3 of 6)
                    Forms\Components\Select::make('directorate_id')
                        ->label('Project Directorate')
                        ->relationship('directorate', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->columnSpan(3),

                    Forms\Components\Select::make('department_id')
                        ->label('Project Department')
                        ->options(fn (Get $get): array => Department::query()
                            ->when(
                                $get('directorate_id'),
                                fn ($q, $id) => $q->where('directorate_id', $id)
                            )
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->nullable()
                        ->columnSpan(3),

                    // Row 4 – 3 columns (each span 2 of 6)
                    Forms\Components\Select::make('project_type')
                        ->label('Project Type')
                        ->options(array_combine(Project::PROJECT_TYPES, Project::PROJECT_TYPES))
                        ->required()
                        ->native(false)
                        ->columnSpan(2),

                    Forms\Components\Select::make('contractor_id')
                        ->label('Project Contractor')
                        ->options(fn (): array => Contractor::query()
                            ->with('user')
                            ->where('firm_type_id', Contractor::TYPE_CONTRACTOR)
                            ->get()
                            ->mapWithKeys(fn (Contractor $c): array => [$c->id => $c->user?->name ?? 'Contractor #'.$c->id])
                            ->all())
                        ->searchable()
                        ->nullable()
                        ->columnSpan(2),

                    Forms\Components\Select::make('consultant_id')
                        ->label('Project Consultant')
                        ->options(fn (): array => Contractor::query()
                            ->with('user')
                            ->where('firm_type_id', Contractor::TYPE_CONSULTANT)
                            ->get()
                            ->mapWithKeys(fn (Contractor $c): array => [$c->id => $c->user?->name ?? 'Consultant #'.$c->id])
                            ->all())
                        ->searchable()
                        ->nullable()
                        ->columnSpan(2),

                    // Row 5 – 2 columns (each span 3 of 6)
                    Forms\Components\TextInput::make('cost')
                        ->label('Project Cost')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->columnSpan(3),

                    Forms\Components\Select::make('office_id')
                        ->label('Project Location')
                        ->relationship('office', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(3),

                    // Row 6 – 3 columns (each span 2 of 6)
                    Forms\Components\DatePicker::make('award_date')
                        ->label('Award Date')
                        ->required()
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('duration')
                        ->label('Project Duration')
                        ->integer()
                        ->minValue(1)
                        ->required()
                        ->columnSpan(2),

                    Forms\Components\Select::make('duration_period')
                        ->label('Duration By')
                        ->options(['days' => 'Days', 'months' => 'Months', 'weeks' => 'Weeks'])
                        ->required()
                        ->native(false)
                        ->columnSpan(2),

                    // Row 7 – full width
                    Forms\Components\FileUpload::make('award_letter')
                        ->label('Upload Award Letter')
                        ->disk('public')
                        ->directory('project_files')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                        ->downloadable()
                        ->openable()
                        ->columnSpanFull(),
                ])
                ->columns(6),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('project_type')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('office.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('directorate.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->searchable(),
                Tables\Columns\TextColumn::make('cost')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('award_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('directorate_id')
                    ->label('Directorate')
                    ->options(fn (): array => Directorate::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('office_id')
                    ->label('Office')
                    ->options(fn (): array => Office::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('project_type')
                    ->label('Project Type')
                    ->options(array_combine(Project::PROJECT_TYPES, Project::PROJECT_TYPES)),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->visible(fn (): bool => auth()->user()?->hasAnyRole([
                    RoleAndPermissions::ADMIN,
                    RoleAndPermissions::ORGANIZATION_ADMIN,
                ]) ?? false),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->paginated([12, 24, 48]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['office', 'directorate', 'department', 'contractor.user', 'consultant.user']);

        return auth()->user()
            ? ProjectAccess::scopeProjects($query, auth()->user())
            : $query->whereRaw('1 = 0');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MilestonesRelationManager::class,
            RelationManagers\ProjectPersonnelRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'report' => Pages\ProjectReport::route('/{record}/report'),
            'view'   => Pages\ViewProject::route('/{record}'),
            'edit'   => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
