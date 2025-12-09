<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\Auth\EditProfile;
use App\Http\Middleware\AdminMiddleware;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckPaymentStatus;
use App\Http\Middleware\RoleBaseMiddleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\AdminPanel\Pages\ShopAppDashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Resources\AdminResource\Widgets\TopRates;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->sidebarCollapsibleOnDesktop()
            // ->sidebarFullyCollapsibleOnDesktop()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Amber,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->spa()
            ->profile(isSimple: false)
            ->font('Poppins')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('News & Events')
                    ->collapsed(),

                // NavigationGroup::make()
                //     ->label('Shop')
                //     ->icon('heroicon-o-building-storefront')
                //     ->collapsed(false),

                // NavigationGroup::make()
                //     ->label('Vetinary Appointment')
                //     ->icon('heroicon-o-document')
                //     ->collapsed(false),

            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sideBarWidth('15rem')
            ->brandLogo(asset('imgs/sdas-logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('imgs/sdas-logo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages(


                [
                    Pages\Dashboard::class,
                ]


            )
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
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
                // CheckPaymentStatus::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ])
            ->authMiddleware([
                RoleBaseMiddleware::class,
                // AdminMiddleware::class
            ])
            // ->plugins([
            //     FilamentShieldPlugin::make(),
            // ]);
            ->databaseNotifications();
    }
}
