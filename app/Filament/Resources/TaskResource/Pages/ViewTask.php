<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\TaskChatMessage;
use App\Models\TaskImage;
use App\Support\ProjectAccess;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.resources.task-resource.pages.view-task';

    public ?array $commentData = [];

    public ?array $imageUploadData = [];

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->imageUploadForm->fill();
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

    public function imageUploadForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('images')
                    ->label('Upload Task Images')
                    ->multiple()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                    ->maxSize(3072)
                    ->disk('public')
                    ->directory('task_images')
                    ->storeFiles(false)
                    ->required(),
            ])
            ->statePath('imageUploadData');
    }

    protected function getForms(): array
    {
        return [
            ...parent::getForms(),
            'commentForm' => $this->commentForm($this->makeForm()),
            'imageUploadForm' => $this->imageUploadForm($this->makeForm()),
        ];
    }

    public function postComment(): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->record->milestone?->project && ProjectAccess::canViewProject(auth()->user(), $this->record->milestone->project), 403);

        $message = trim((string) ($this->commentData['message'] ?? ''));

        validator(['message' => $message], [
            'message' => ['required', 'string', 'max:5000'],
        ])->validate();

        TaskChatMessage::query()->create([
            'task_id' => $this->record->id,
            'sender_id' => auth()->id(),
            'message' => $message,
            'created_by_id' => auth()->id(),
        ]);

        $this->commentData = [];
        $this->commentForm->fill();

        Notification::make()->title('Comment posted')->success()->send();
    }

    public function uploadImages(): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->record->milestone?->project && ProjectAccess::canViewProject(auth()->user(), $this->record->milestone->project), 403);

        $data = $this->imageUploadForm->getState();

        foreach (($data['images'] ?? []) as $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                continue;
            }

            $path = $file->store('task_images', 'public');

            TaskImage::query()->create([
                'task_id' => $this->record->id,
                'uploader_id' => auth()->id(),
                'name' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => Storage::disk('public')->mimeType($path),
                'size' => Storage::disk('public')->size($path),
            ]);
        }

        $this->imageUploadData = [];
        $this->imageUploadForm->fill();

        Notification::make()->title('Images uploaded')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }
}
