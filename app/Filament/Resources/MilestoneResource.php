<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilestoneResource\Pages;
use App\Filament\Resources\MilestoneResource\RelationManagers;
use App\Models\Milestone;
use App\Support\ProjectAccess;
use Filament\Forms;
use Filament\Forms\Form;
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
            Forms\Components\Select::make('project_id')->relationship('project', 'name')->searchable()->preload()->required(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('amount')->numeric()->default(0)->required(),
            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('tasks_count')->label('Tasks')->counts('tasks')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
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
        $query = parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class])->with('project');

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('project', fn (Builder $projects): Builder => ProjectAccess::scopeProjects($projects, auth()->user()));
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
            'view' => Pages\ViewMilestone::route('/{record}'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }
}
