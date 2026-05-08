<?php

namespace App\Filament\Resources;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Milestone;
use App\Models\Task;
use App\Support\ProjectAccess;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('cost_info')
                ->label('')
                ->content(function (Get $get, ?Task $record = null): string {
                    $milestoneId = $get('milestone_id')
                        ?? request()->query('milestone_id')
                        ?? $record?->milestone_id;

                    $milestoneUlid = request()->query('milestone_ulid');

                    $milestone = $milestoneUlid
                        ? Milestone::query()->where('ulid', $milestoneUlid)->first()
                        : ($milestoneId ? Milestone::query()->find($milestoneId) : null);

                    if (! $milestone) {
                        return '';
                    }

                    $usedCost = (float) $milestone->tasks()
                        ->whereNull('deleted_at')
                        ->when($record?->id, fn (Builder $query): Builder => $query->whereKeyNot($record->id))
                        ->sum('cost');

                    $remaining = max(0, (float) $milestone->amount - $usedCost);

                    return 'The max cost for task that can be added is: '.number_format($remaining, 2);
                })
                ->columnSpanFull()
                ->extraAttributes([
                    'style' => 'background:#d1fae5; border-left:4px solid #16A34A; padding:12px 16px; border-radius:8px; font-size:14px; color:#374151; font-weight:500;',
                ]),

            Forms\Components\Hidden::make('milestone_id'),

            Forms\Components\TextInput::make('name')
                ->label('Task Name')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->required()
                ->rows(3)
                ->columnSpanFull(),

            Forms\Components\DatePicker::make('start_date')
                ->label('Start Date')
                ->required()
                ->rules(fn (Get $get, ?Task $record): array => [
                    function (string $attribute, mixed $value, \Closure $fail) use ($get, $record) {
                        if (! $value) {
                            return;
                        }

                        $milestoneId = $get('milestone_id') ?? $record?->milestone_id;
                        $milestone = $milestoneId ? Milestone::with('project')->find($milestoneId) : null;
                        $project = $milestone?->project;

                        if (! $project?->award_date) {
                            return;
                        }

                        if (Carbon::parse($value)->lt($project->award_date)) {
                            $fail(
                                'Task dates must fall within the project timeline: '.
                                $project->award_date->format('d M Y').' to '.$project->end_date->format('d M Y')
                            );
                        }
                    },
                ]),

            Forms\Components\DatePicker::make('due_date')
                ->label('Due Date')
                ->required()
                ->afterOrEqual('start_date')
                ->rules(fn (Get $get, ?Task $record): array => [
                    function (string $attribute, mixed $value, \Closure $fail) use ($get, $record) {
                        if (! $value) {
                            return;
                        }

                        $milestoneId = $get('milestone_id') ?? $record?->milestone_id;
                        $milestone = $milestoneId ? Milestone::with('project')->find($milestoneId) : null;
                        $project = $milestone?->project;

                        if (! $project?->award_date) {
                            return;
                        }

                        $projEnd = $project->end_date;

                        if (Carbon::parse($value)->gt($projEnd)) {
                            $fail(
                                'Task dates must fall within the project timeline: '.
                                $project->award_date->format('d M Y').' to '.$projEnd->format('d M Y')
                            );
                        }
                    },
                ]),

            Forms\Components\TextInput::make('cost')
                ->label('Cost')
                ->numeric()
                ->minValue(0)
                ->required()
                ->rules(fn (Get $get, ?Task $record): array => [
                    function (string $attribute, mixed $value, \Closure $fail) use ($get, $record) {
                        $milestoneId = $get('milestone_id') ?? $record?->milestone_id;

                        if (! $milestoneId) {
                            return;
                        }

                        $milestone = Milestone::find($milestoneId);

                        if (! $milestone) {
                            return;
                        }

                        $existingCost = (float) Task::query()
                            ->where('milestone_id', $milestoneId)
                            ->whereNull('deleted_at')
                            ->when($record?->id, fn ($q) => $q->where('id', '!=', $record->id))
                            ->sum('cost');

                        $newTotal = $existingCost + (float) $value;

                        if ($newTotal > (float) $milestone->amount) {
                            $remaining = max(0, (float) $milestone->amount - $existingCost);
                            $fail(
                                'The total task cost cannot exceed the milestone amount of ₦'.number_format((float) $milestone->amount, 2).'. '.
                                'Remaining available: ₦'.number_format($remaining, 2)
                            );
                        }
                    },
                ]),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('milestone.name')->label('Milestone')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('milestone.project.name')->label('Project')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('due_date')->date(),
                Tables\Columns\TextColumn::make('cost')->money('NGN'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'done' => 'Done']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('markDone')
                    ->label('Mark as Done')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Task $record): bool => $record->status !== 'done'
                        && auth()->check()
                        && Task::canBeMarkedDoneBy(auth()->user(), $record))
                    ->action(function (Task $record): void {
                        $record->mark_as_done();
                        Notification::make()->title('Task marked as done')->success()->send();
                    }),
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
            ->with('milestone.project');

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas(
            'milestone.project',
            fn (Builder $projects): Builder => ProjectAccess::scopeProjects($projects, auth()->user())
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
