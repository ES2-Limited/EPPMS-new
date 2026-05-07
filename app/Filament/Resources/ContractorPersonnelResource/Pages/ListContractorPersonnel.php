<?php

namespace App\Filament\Resources\ContractorPersonnelResource\Pages;

use App\Filament\Resources\ContractorPersonnelResource;
use App\Filament\Widgets\FirmStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContractorPersonnel extends ListRecords
{
    protected static string $resource = ContractorPersonnelResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getHeaderWidgets(): array
    {
        return [FirmStatsWidget::class];
    }
}
