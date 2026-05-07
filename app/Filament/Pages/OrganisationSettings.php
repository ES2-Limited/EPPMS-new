<?php

namespace App\Filament\Pages;

use App\Constants\RoleAndPermissions;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class OrganisationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Organisation Settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.organisation-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->form->fill(Organization::query()->first()?->attributesToArray() ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organisation Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Organisation Name')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages(['required' => 'Enter the organisation name.']),
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->disk('public')
                            ->directory('organisation')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Recommended: PNG or JPG, max 2MB.'),
                        Forms\Components\Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->validationMessages(['email' => 'Enter a valid organisation email address.']),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255)
                            ->validationMessages(['url' => 'Enter a valid website URL, including https://.']),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);

        $data = $this->form->getState();
        $organization = Organization::query()->firstOrNew();

        $organization->fill($data);
        $organization->save();

        $this->form->fill($organization->fresh()?->attributesToArray() ?? []);

        Notification::make()->title('Organisation settings saved')->success()->send();
    }
}
