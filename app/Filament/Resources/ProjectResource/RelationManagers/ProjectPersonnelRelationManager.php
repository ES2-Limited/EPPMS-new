<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Constants\RoleAndPermissions;
use App\Mail\ProjectAssignmentMail;
use App\Models\Personnel;
use App\Models\ProjectPersonnel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProjectPersonnelRelationManager extends RelationManager
{
    protected static string $relationship = 'projectPersonnel';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('personnel_id')
                ->label('Personnel')
                ->options(fn (): array => Personnel::query()->with('user')->get()->mapWithKeys(fn (Personnel $personnel): array => [$personnel->id => trim(($personnel->user?->name ?? 'Personnel #'.$personnel->id).' - '.($personnel->user?->email ?? ''))])->all())
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('project_role')
                ->options([
                    RoleAndPermissions::PROJECT_MANAGER => 'Project Manager',
                    RoleAndPermissions::PROJECT_MEMBER => 'Project Member',
                ])
                ->required()
                ->native(false),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withoutGlobalScopes([SoftDeletingScope::class])->with(['personnel.user', 'personnel.directorate', 'personnel.department']))
            ->columns([
                Tables\Columns\TextColumn::make('personnel.user.name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('personnel.user.email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('personnel.directorate.name')->label('Directorate')->searchable(),
                Tables\Columns\TextColumn::make('personnel.department.name')->label('Department')->searchable(),
                Tables\Columns\TextColumn::make('project_role')->label('Project Role')->badge()->formatStateUsing(fn (?string $state): string => str($state ?? '')->replace('_', ' ')->title()->toString()),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(fn (Model $record) => $this->sendAssignmentEmail($record)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ]);
    }

    protected function sendAssignmentEmail(ProjectPersonnel $record): void
    {
        try {
            Mail::to($record->personnel->user)->send(new ProjectAssignmentMail($record->personnel->user, $record->project, $record->project_role));
        } catch (Throwable $exception) {
            Log::warning('Project assignment email failed.', [
                'project_personnel_id' => $record->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
