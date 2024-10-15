<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Partner;
use App\Models\Post;
use App\Models\User;
use App\Models\Proposal;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UsersCont extends BaseWidget
{

    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Projects', Post::count())
            ->description('Συνολική καταμέτρηση Έργων')
            ->descriptionIcon('heroicon-o-book-open', IconPosition::Before)
            ->chart([0, Post::count()])
            ->color('primary')
            ->url("/admin/posts"),
            Stat::make('Proposals', Proposal::count())
            ->description('Συνολικός αριθμός προτάσεων')
            ->descriptionIcon('heroicon-o-academic-cap', IconPosition::Before)
            ->chart([0, Proposal::count()])
            ->color('danger')
            ->url("/admin/proposals"),
            Stat::make('Categories', Category::count())
            ->description('Σύνολο Κατηγοριών')
            ->descriptionIcon('heroicon-o-rectangle-stack', IconPosition::Before)
            ->chart([0, Category::count()])
            ->color('info')
            ->url("/admin/categories"),
            Stat::make('System Users', User::count())
            ->description('Συνολικός αριθμός Χρηστών')
            ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
            ->chart([0, User::count()])
            ->color('warning')
            ->url("/admin/users"),
            Stat::make('Admins', User::where('isAdmin', true)->count())
            ->description('Συνολικός αριθμός Διαχειρηστών')
            ->descriptionIcon('heroicon-o-user-circle', IconPosition::Before)
            ->chart([0, User::where('isAdmin', true)->count()])
            ->color('success')
            ->url("/admin/users?tableFilters[isAdmin][value]=1"),
            Stat::make('Partners', Partner::count())
            ->description('Συνολικοί Συνεργάτες')
            ->descriptionIcon('heroicon-o-globe-alt', IconPosition::Before)
            ->chart([0, Partner::count()])
            ->color('gray')
            ->url("/admin/partners")
        ];
    }
}
