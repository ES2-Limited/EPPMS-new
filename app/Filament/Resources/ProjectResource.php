<?php

namespace App\Filament\Resources;

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
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

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
                    Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                    Forms\Components\Select::make('status')->options(array_combine(Project::STATUSES, array_map(fn (string $status): string => str($status)->replace('_', ' ')->title()->toString(), Project::STATUSES)))->required()->native(false),
                    Forms\Components\TextInput::make('priority')->maxLength(255),
                    Forms\Components\Textarea::make('description')->rows(4)->columnSpanFull(),
                ])
                ->columns(2),
            Forms\Components\Section::make('Financials')
                ->schema([
                    Forms\Components\TextInput::make('cost')->numeric()->default(0)->required()->live(onBlur: true),
                    Forms\Components\TextInput::make('total_paid')->numeric()->default(0)->required()->live(onBlur: true),
                    Forms\Components\TextInput::make('total_left')->numeric()->disabled()->dehydrated(false)->formatStateUsing(fn (?Project $record, $state): string => number_format((float) ($record?->total_left ?? $state ?? 0), 2)),
                ])
                ->columns(3),
            Forms\Components\Section::make('Award')
                ->schema([
                    Forms\Components\DatePicker::make('award_date'),
                    Forms\Components\TextInput::make('duration')->integer()->minValue(1),
                    Forms\Components\Select::make('duration_period')->options(array_combine(Project::DURATION_PERIODS, array_map('ucfirst', Project::DURATION_PERIODS)))->native(false),
                    Forms\Components\FileUpload::make('award_letter')
                        ->disk('public')
                        ->directory('project_files')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                        ->downloadable()
                        ->openable(),
                ])
                ->columns(4),
            Forms\Components\Section::make('Organisation and Firm')
                ->schema([
                    Forms\Components\Select::make('office_id')->relationship('office', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('directorate_id')->relationship('directorate', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('contractor_id')
                        ->label('Contractor')
                        ->options(fn (): array => Contractor::query()->with('user')->whereIn('firm_type_id', [0, 1])->get()->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Contractor #'.$contractor->id])->all())
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('consultant_id')
                        ->label('Consultant')
                        ->options(fn (): array => Contractor::query()->with('user')->where('firm_type_id', Contractor::TYPE_CONSULTANT)->get()->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Consultant #'.$contractor->id])->all())
                        ->searchable()
                        ->preload(),
                ])
                ->columns(3),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Project Details')
                ->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('priority'),
                    Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('progress')->label('Progress')->state(fn (Project $record): string => $record->get_progress().'%'),
                    Infolists\Components\TextEntry::make('time_left')->label('Time Left'),
                ])->columns(3),
            Infolists\Components\Section::make('Award and Finance')
                ->schema([
                    Infolists\Components\TextEntry::make('award_date')->date(),
                    Infolists\Components\TextEntry::make('award_letter')->label('Award Letter')->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)->openUrlInNewTab(),
                    Infolists\Components\TextEntry::make('cost')->money('NGN'),
                    Infolists\Components\TextEntry::make('total_paid')->money('NGN'),
                    Infolists\Components\TextEntry::make('total_left')->money('NGN'),
                ])->columns(3),
            Infolists\Components\Section::make('Assignments')
                ->schema([
                    Infolists\Components\TextEntry::make('office.name')->label('Office'),
                    Infolists\Components\TextEntry::make('directorate.name')->label('Directorate'),
                    Infolists\Components\TextEntry::make('department.name')->label('Department'),
                    Infolists\Components\TextEntry::make('contractor.user.name')->label('Contractor'),
                    Infolists\Components\TextEntry::make('consultant.user.name')->label('Consultant'),
                    Infolists\Components\TextEntry::make('milestones_count')->label('Milestones')->state(fn (Project $record): int => $record->milestones()->count()),
                    Infolists\Components\TextEntry::make('project_personnel_count')->label('Project Personnel')->state(fn (Project $record): int => $record->projectPersonnel()->count()),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('office.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('directorate.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('department.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contractor.user.name')->label('Contractor')->searchable(),
                Tables\Columns\TextColumn::make('consultant.user.name')->label('Consultant')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->searchable(),
                Tables\Columns\TextColumn::make('priority')->searchable(),
                Tables\Columns\TextColumn::make('description')->searchable()->limit(40)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cost')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('total_paid')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('total_left')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('progress')->label('Progress')->state(fn (Project $record): string => $record->get_progress().'%'),
                Tables\Columns\TextColumn::make('award_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(array_combine(Project::STATUSES, Project::STATUSES)),
                Tables\Filters\SelectFilter::make('office_id')->label('Office')->options(fn (): array => Office::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('directorate_id')->label('Directorate')->options(fn (): array => Directorate::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('department_id')->label('Department')->options(fn (): array => Department::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('contractor_id')->label('Contractor')->options(fn (): array => Contractor::query()->with('user')->whereIn('firm_type_id', [0, 1])->get()->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Contractor #'.$contractor->id])->all()),
                Tables\Filters\SelectFilter::make('consultant_id')->label('Consultant')->options(fn (): array => Contractor::query()->with('user')->where('firm_type_id', Contractor::TYPE_CONSULTANT)->get()->mapWithKeys(fn (Contractor $contractor): array => [$contractor->id => $contractor->user?->name ?? 'Consultant #'.$contractor->id])->all()),
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
            ->with(['office', 'directorate', 'department', 'contractor.user', 'consultant.user']);

        return auth()->user() ? ProjectAccess::scopeProjects($query, auth()->user()) : $query->whereRaw('1 = 0');
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
