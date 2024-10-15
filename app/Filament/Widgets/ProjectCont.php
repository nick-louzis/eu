<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProjectCont extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Stat::make('Projects', Post::count())
            // ->description('Number of total projects')
            // ->descriptionIcon('heroicon-o-book-open', IconPosition::Before)
            // ->chart([Post::count(),Post::count()+10])
            // ->color('success')
        ];
    }
}
