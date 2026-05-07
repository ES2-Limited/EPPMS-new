<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\ProjectChat;
use App\Support\ProjectAccess;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.view-project';

    public ?array $commentData = [];

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->commentForm->fill();
    }

    public function commentForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->label('Comment')
                    ->required()
                    ->maxLength(5000)
                    ->rows(3),
            ])
            ->statePath('commentData');
    }

    protected function getForms(): array
    {
        return [
            ...parent::getForms(),
            'commentForm' => $this->commentForm($this->makeForm()),
        ];
    }

    public function postComment(): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless(ProjectAccess::canViewProject(auth()->user(), $this->record), 403);

        $message = trim((string) ($this->commentData['message'] ?? ''));

        validator(['message' => $message], [
            'message' => ['required', 'string', 'max:5000'],
        ])->validate();

        ProjectChat::query()->create([
            'project_id' => $this->record->id,
            'sender_id' => auth()->id(),
            'message' => $message,
            'created_by_id' => auth()->id(),
        ]);

        $this->commentData = [];
        $this->commentForm->fill();

        Notification::make()->title('Comment posted')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
