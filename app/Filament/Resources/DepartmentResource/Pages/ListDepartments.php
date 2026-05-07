<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use App\Filament\Widgets\OrgListHeaderWidget;
use App\Filament\Widgets\OrgStatsBarWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    public function getSubheading(): string
    {
        return 'Document contains the list of registered departments';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Department'),
            Actions\Action::make('print')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->url(route('admin.departments.records.print'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrgListHeaderWidget::make([
                'heading' => 'Departments',
                'subheading' => $this->getSubheading(),
            ]),
            OrgStatsBarWidget::class,
        ];
    }
}
