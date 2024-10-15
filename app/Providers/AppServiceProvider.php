<?php

namespace App\Providers;

// use App\Models\Post;
// use App\Observers\ProjectObserver;
use Illuminate\Support\ServiceProvider;
 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
    }

    // public function boot(): void {
    //     Post::observe(ProjectObserver::class);
    // }
    
}
