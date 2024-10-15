<?php

namespace App\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Support\Assets\Js;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentAsset;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    // public function mount(): void
    // {
        
    // }

    // public function boot(): void {
    //     FilamentAsset::register([
    //         Js::make('changeTDColor', __DIR__ . '../../../../../../resources/js/changeTDColor.js'),
    //     ]);
    // }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
