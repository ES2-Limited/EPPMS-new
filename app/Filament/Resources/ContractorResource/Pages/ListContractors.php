<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Filament\Resources\ContractorResource;
use App\Filament\Widgets\FirmListHeaderWidget;
use Filament\Resources\Pages\ListRecords;

class ListContractors extends ListRecords
{
    protected static string $resource = ContractorResource::class;

    public function getSubheading(): string
    {
        return 'Document contains the list of registered firms';
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FirmListHeaderWidget::make([
                'heading' => (app_organisation()?->name ?? 'ePPMS').' Firms',
                'subheading' => $this->getSubheading(),
                'addLabel' => 'Add Firm',
                'addUrl' => ContractorResource::getUrl('create'),
                'printUrl' => route('admin.firms.records.print'),
                'backLabel' => 'Firms Report',
                'backUrl' => ContractorResource::getUrl('index'),
            ]),
        ];
    }
}
