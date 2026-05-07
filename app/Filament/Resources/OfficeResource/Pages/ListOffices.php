<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use App\Filament\Widgets\OrgListHeaderWidget;
use App\Filament\Widgets\OrgStatsBarWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffices extends ListRecords
{
    protected static string $resource = OfficeResource::class;

    public function getSubheading(): string
    {
        return 'Document contains the list of registered offices';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Office'),
            Actions\Action::make('print')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->url(route('admin.offices.records.print'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrgListHeaderWidget::make([
                'heading' => 'Office Location',
                'subheading' => $this->getSubheading(),
            ]),
            OrgStatsBarWidget::class,
        ];
    }
}
