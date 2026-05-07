<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use App\Filament\Widgets\OrgListHeaderWidget;
use App\Filament\Widgets\OrgStatsBarWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    public function getSubheading(): string
    {
        return 'Document contains the list of registered units';
    }

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Add Unit')];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrgListHeaderWidget::make([
                'heading' => 'Units',
                'subheading' => $this->getSubheading(),
            ]),
            OrgStatsBarWidget::class,
        ];
    }
}
