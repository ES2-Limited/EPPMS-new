<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Filament\Resources\MilestoneResource;
use App\Models\Milestone;
use App\Support\ProjectAccess;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('amount')->numeric()->minValue(0)->default(0)->required(),
            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn ($query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('description')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('tasks_count')->label('Tasks')->counts('tasks')->sortable(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->authorize(fn (): bool => auth()->check() && ProjectAccess::canManageMilestone(auth()->user(), $this->getOwnerRecord())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Milestone $record): string => MilestoneResource::getUrl('view', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
