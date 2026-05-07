<?php

namespace App\Filament\Resources\MilestoneResource\RelationManagers;

use App\Models\Task;
use App\Support\ProjectAccess;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            Forms\Components\Select::make('status')->options(['pending' => 'Pending', 'done' => 'Done'])->default('pending')->required()->native(false),
            Forms\Components\DatePicker::make('start_date'),
            Forms\Components\DatePicker::make('due_date')->afterOrEqual('start_date'),
            Forms\Components\TextInput::make('cost')->numeric()->minValue(0)->default(0)->required(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('due_date')->date(),
                Tables\Columns\TextColumn::make('cost')->money('NGN'),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->authorize(fn (): bool => auth()->check() && ProjectAccess::canManageMilestone(auth()->user(), $this->getOwnerRecord()->project)),
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
            ]);
    }
}
