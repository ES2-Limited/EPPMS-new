<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class OrgListHeaderWidget extends Widget
{
    protected static string $view = 'filament.widgets.org-list-header-widget';

    protected static ?int $sort = -1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public string $heading = 'Organisation Records';

    public string $subheading = 'Document contains the list of registered records';

    protected function getViewData(): array
    {
        return [
            'organisation' => app_organisation(),
            'heading' => $this->heading,
            'subheading' => $this->subheading,
        ];
    }
}
