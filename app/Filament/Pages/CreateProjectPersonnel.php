<?php

namespace App\Filament\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ProjectResource;
use App\Mail\ProjectAssignmentMail;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\Personnel;
use App\Models\Project;
use App\Models\ProjectPersonnel;
use App\Support\ProjectAccess;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CreateProjectPersonnel extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $isDiscovered = false;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.create-project-personnel';

    public Project $projectRecord;

    public ?array $data = [];

    public function mount(string $project): void
    {
        abort_unless(auth()->check(), 403);

        $this->projectRecord = Project::query()
            ->where('ulid', $project)
            ->orWhere('id', $project)
            ->firstOrFail();

        abort_unless(ProjectAccess::canManageProject(auth()->user()), 403);
        abort_unless(ProjectAccess::canViewProject(auth()->user(), $this->projectRecord), 403);

        $this->form->fill();
    }

    public function getTitle(): string
    {
        return $this->projectRecord->name.' - Project Personnel';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Add Project Personnel')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 1, 'md' => 3])
                            ->schema([
                                Forms\Components\Select::make('directorate_id')
                                    ->label('Personnel Directorate')
                                    ->placeholder('Choose directorate')
                                    ->options(fn (): array => Directorate::query()->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Forms\Components\Select::make('department_id')
                                    ->label('Personnel Department')
                                    ->placeholder('department')
                                    ->options(fn (Forms\Get $get): array => Department::query()
                                        ->when($get('directorate_id'), fn (Builder $query, $directorateId): Builder => $query->where('directorate_id', $directorateId))
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Forms\Components\Select::make('personnel_id')
                                    ->label('Project Personnel')
                                    ->placeholder('Personnel')
                                    ->options(fn (Forms\Get $get): array => Personnel::query()
                                        ->with('user')
                                        ->when($get('directorate_id'), fn (Builder $query, $directorateId): Builder => $query->where('directorate_id', $directorateId))
                                        ->when($get('department_id'), fn (Builder $query, $departmentId): Builder => $query->where('department_id', $departmentId))
                                        ->get()
                                        ->mapWithKeys(fn (Personnel $personnel): array => [$personnel->id => $personnel->user?->name ?? 'Personnel #'.$personnel->id])
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('project_role')
                            ->label('Personnel Role')
                            ->placeholder('Choose Role')
                            ->options([
                                RoleAndPermissions::PROJECT_MANAGER => 'project_manager',
                                RoleAndPermissions::PROJECT_MEMBER => 'project_member',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        abort_unless(ProjectAccess::canManageProject(auth()->user()), 403);

        $data = $this->form->getState();

        $assignment = ProjectPersonnel::query()->create([
            'project_id' => $this->projectRecord->id,
            'personnel_id' => $data['personnel_id'],
            'project_role' => $data['project_role'],
        ]);

        try {
            if ($assignment->personnel?->user) {
                Mail::to($assignment->personnel->user)->queue(new ProjectAssignmentMail(
                    $assignment->personnel->user,
                    $this->projectRecord,
                    $assignment->project_role,
                    ProjectResource::getUrl('view', ['record' => $this->projectRecord]),
                ));
            }
        } catch (Throwable $exception) {
            Log::warning('Project assignment email failed.', [
                'project_personnel_id' => $assignment->id,
                'error' => $exception->getMessage(),
            ]);
        }

        Notification::make()->title('Project personnel added')->success()->send();

        $this->redirect(route('filament.admin.pages.project-personnels', ['project' => $this->projectRecord->ulid]));
    }
}
