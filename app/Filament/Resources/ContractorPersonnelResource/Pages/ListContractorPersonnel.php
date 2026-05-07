<?php

namespace App\Filament\Resources\ContractorPersonnelResource\Pages;

use App\Filament\Resources\ContractorPersonnelResource;
use App\Filament\Widgets\FirmListHeaderWidget;
use Filament\Resources\Pages\ListRecords;

class ListContractorPersonnel extends ListRecords
{
    protected static string $resource = ContractorPersonnelResource::class;

    public function getSubheading(): string
    {
        return 'Document contains the list of registered contractor personnels';
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
                'heading' => 'Contractor Personnel',
                'subheading' => $this->getSubheading(),
                'addLabel' => 'Add Contractor Personnel',
                'addUrl' => ContractorPersonnelResource::getUrl('create'),
            ]),
        ];
    }
}
