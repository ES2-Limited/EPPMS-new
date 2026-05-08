<?php

namespace App\Filament\Resources;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\MilestoneResource\Pages;
use App\Filament\Resources\MilestoneResource\RelationManagers;
use App\Models\Milestone;
use App\Models\Project;
use App\Support\ProjectAccess;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('amount_info')
                ->label('')
                ->content(function (Get $get, ?Milestone $record = null): string {
                    $projectId = $get('project_id')
                        ?? request()->query('project_id')
                        ?? $record?->project_id;

                    $projectUlid = request()->query('project_ulid');

                    $project = $projectUlid
                        ? Project::query()->where('ulid', $projectUlid)->first()
                        : ($projectId ? Project::query()->find($projectId) : null);

                    if (! $project) {
                        return '';
                    }

                    $used = (float) $project->milestones()
                        ->whereNull('deleted_at')
                        ->when($record?->id, fn (Builder $query): Builder => $query->whereKeyNot($record->id))
                        ->sum('amount');

                    $remaining = max(0, (float) $project->cost - $used);

                    return 'The max amount for milestone that can be added is: '.number_format($remaining, 2);
                })
                ->columnSpanFull()
                ->extraAttributes([
                    'style' => 'background:#ede9fe; border-left:4px solid #5B5FC7; padding:12px 16px; border-radius:8px; font-size:14px; color:#374151; font-weight:500;',
                ]),

            Forms\Components\Hidden::make('project_id'),

            Forms\Components\TextInput::make('name')
                ->label('Milestone Name')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->numeric()
                ->minValue(0)
                ->required()
                ->rules(fn (Get $get, ?Milestone $record): array => [
                    function (string $attribute, mixed $value, \Closure $fail) use ($get, $record) {
                        $projectId = $get('project_id') ?? $record?->project_id;

                        if (! $projectId) {
                            return;
                        }

                        $project = Project::find($projectId);

                        if (! $project) {
                            return;
                        }

                        $existingSum = (float) Milestone::query()
                            ->where('project_id', $projectId)
                            ->whereNull('deleted_at')
                            ->when($record?->id, fn ($q) => $q->where('id', '!=', $record->id))
                            ->sum('amount');

                        $newTotal = $existingSum + (float) $value;

                        if ($newTotal > (float) $project->cost) {
                            $remaining = max(0, (float) $project->cost - $existingSum);
                            $fail(
                                'The total milestone amount cannot exceed the project contract sum of ₦'.number_format((float) $project->cost, 2).'. '.
                                'Remaining available: ₦'.number_format($remaining, 2)
                            );
                        }
                    },
                ]),

            Forms\Components\DatePicker::make('start_date')
                ->label('Start Date')
                ->required()
                ->rules(fn (Get $get, ?Milestone $record): array => [
                    function (string $attribute, mixed $value, \Closure $fail) use ($get, $record) {
                        if (! $value) {
                            return;
                        }

                        $projectId = $get('project_id') ?? $record?->project_id;
                        $project = $projectId ? Project::find($projectId) : null;

                        if (! $project?->award_date) {
                            return;
                        }

                        if (Carbon::parse($value)->lt($project->award_date)) {
                            $fail(
                                'Milestone dates must fall within the project timeline: '.
                                $project->award_date->format('d M Y').' to '.$project->end_date->format('d M Y')
                            );
                        }
                    },
                ]),

            Forms\Components\DatePicker::make('end_date')
                ->label('End Date')
                ->required()
                ->afterOrEqual('start_date')
                ->rules(fn (Get $get, ?Milestone $record): array => [
                    function (string $attribute, mixed $value, \Closure $fail) use ($get, $record) {
                        if (! $value) {
                            return;
                        }

                        $projectId = $get('project_id') ?? $record?->project_id;
                        $project = $projectId ? Project::find($projectId) : null;

                        if (! $project?->award_date) {
                            return;
                        }

                        $projEnd = $project->end_date;

                        if (Carbon::parse($value)->gt($projEnd)) {
                            $fail(
                                'Milestone dates must fall within the project timeline: '.
                                $project->award_date->format('d M Y').' to '.$projEnd->format('d M Y')
                            );
                        }
                    },
                ]),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->required()
                ->rows(3)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('tasks_count')->label('Tasks')->counts('tasks')->sortable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
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
            ->paginated([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with('project');

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas(
            'project',
            fn (Builder $projects): Builder => ProjectAccess::scopeProjects($projects, auth()->user())
        );
    }

    public static function getRelations(): array
    {
        return [RelationManagers\TasksRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMilestones::route('/'),
            'create' => Pages\CreateMilestone::route('/create'),
            'report' => Pages\MilestoneReport::route('/{record}/report'),
            'view' => Pages\ViewMilestone::route('/{record}'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }
}
