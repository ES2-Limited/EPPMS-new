<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Milestone;
use App\Models\Task;
use App\Support\ProjectAccess;
use Filament\Forms;
use Filament\Forms\Form;
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
            Forms\Components\Select::make('milestone_id')->relationship('milestone', 'name')->searchable()->preload()->required(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            Forms\Components\Select::make('status')->options(['pending' => 'Pending', 'done' => 'Done'])->default('pending')->required()->native(false),
            Forms\Components\DatePicker::make('start_date'),
            Forms\Components\DatePicker::make('due_date')->afterOrEqual('start_date'),
            Forms\Components\TextInput::make('cost')->numeric()->minValue(0)->default(0)->required(),
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
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('milestone_id')->label('Milestone')->options(fn (): array => Milestone::query()->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'done' => 'Done']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('markDone')
                    ->label('Mark as Done')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Task $record): bool => $record->status !== 'done' && auth()->check() && Task::canBeMarkedDoneBy(auth()->user(), $record))
                    ->action(function (Task $record): void {
                        $record->mark_as_done();
                        Notification::make()->title('Task marked as done')->success()->send();
                    }),
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
        $query = parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class])->with('milestone.project');

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('milestone.project', fn (Builder $projects): Builder => ProjectAccess::scopeProjects($projects, auth()->user()));
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
