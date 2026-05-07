<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class FirmListHeaderWidget extends Widget
{
    protected static string $view = 'filament.widgets.firm-list-header-widget';

    protected static ?int $sort = -1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public string $heading = 'Firms';

    public string $subheading = 'Document contains the list of registered firms';

    public string $addLabel = 'Add Firm';

    public string $addUrl = '#';

    public ?string $printUrl = null;

    public ?string $backLabel = null;

    public ?string $backUrl = null;

    protected function getViewData(): array
    {
        return [
            'organisation' => app_organisation(),
            'heading' => $this->heading,
            'subheading' => $this->subheading,
            'addLabel' => $this->addLabel,
            'addUrl' => $this->addUrl,
            'printUrl' => $this->printUrl,
            'backLabel' => $this->backLabel,
            'backUrl' => $this->backUrl,
        ];
    }
}
