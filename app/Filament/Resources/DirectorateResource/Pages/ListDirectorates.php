<?php

namespace App\Filament\Resources\DirectorateResource\Pages;

use App\Filament\Resources\DirectorateResource;
use App\Filament\Widgets\OrgListHeaderWidget;
use App\Filament\Widgets\OrgStatsBarWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDirectorates extends ListRecords
{
    protected static string $resource = DirectorateResource::class;

    public function getSubheading(): string
    {
        return 'Document contains the list of registered directorates';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Directorate'),
            Actions\Action::make('print')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->url(route('admin.directorates.records.print'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrgListHeaderWidget::make([
                'heading' => 'Directorates',
                'subheading' => $this->getSubheading(),
            ]),
            OrgStatsBarWidget::class,
        ];
    }
}
