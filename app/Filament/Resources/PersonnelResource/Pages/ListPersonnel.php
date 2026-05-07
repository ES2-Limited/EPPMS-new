<?php

namespace App\Filament\Resources\PersonnelResource\Pages;

use App\Filament\Resources\PersonnelResource;
use App\Filament\Widgets\OrgListHeaderWidget;
use App\Filament\Widgets\OrgStatsBarWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonnel extends ListRecords
{
    protected static string $resource = PersonnelResource::class;

    public function getSubheading(): string
    {
        return 'Document contains the list of registered personnels';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Personnel'),
            Actions\Action::make('print')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->url(route('admin.personnels.records.print'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrgListHeaderWidget::make([
                'heading' => 'Personnels',
                'subheading' => $this->getSubheading(),
            ]),
            OrgStatsBarWidget::class,
        ];
    }
}
