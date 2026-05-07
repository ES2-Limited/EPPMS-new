<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OngoingProjectsWidget;
use App\Filament\Widgets\ProjectHistogramWidget;
use App\Filament\Widgets\ProjectStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = '';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            OngoingProjectsWidget::class,
            ProjectStatsWidget::class,
            ProjectHistogramWidget::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return 1;
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getHeading(): string
    {
        return '';
    }
}
