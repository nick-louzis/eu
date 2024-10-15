<?php

namespace App\Providers\Filament;

use Auth\Register;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\ChartTest;
use App\Filament\Widgets\UsersCont;
use App\Providers\AppServiceProvider;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{

    public function boot()
    {
        FilamentAsset::register([
            Css::make('filament-custom',  __DIR__ . '../../../../resources/css/filament-custom.css'),
            Js::make('changeTDColor', __DIR__ . '../../../../resources/js/changeTDColor.js'),
        ]);
        // dd(file_exists(__DIR__ . '../../../../resources/css/filament-custom.css'));
    }
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Eu Projects Manager')
            ->brandLogo(asset('images/eu.png'))
            ->brandLogoHeight('60px')
            ->login()
            ->profile()
            ->databaseNotifications()
            // ->passwordReset()
            // ->colors([
            //     'danger' => Color::Rose,
            //     'gray' => Color::Gray,
            //     'info' => Color::Amber,
            //     'primary' =>  Color::PrimaryLightMode,
            //     'success' => Color::Emerald,
            //     'warning' => Color::Orange,
            // ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            // ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ChartTest::class,
                UsersCont::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
