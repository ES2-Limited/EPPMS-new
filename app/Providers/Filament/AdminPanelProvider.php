<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ProjectResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName(fn () => app_organisation()?->name ?? 'ePPMS')
            ->brandLogo(fn () => app_organisation()?->logo_url)
            ->brandLogoHeight('48px')
            ->colors([
                'primary' => Color::hex('#5B5FC7'),
                'gray' => Color::Gray,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->topNavigation(false)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('
                    <style>
                        .fi-topbar {
                            background-color: #0D1B3E !important;
                        }
                        .fi-topbar-breadcrumbs,
                        .fi-topbar .fi-icon-btn,
                        .fi-topbar .fi-user-avatar-wrapper,
                        .fi-topbar-item {
                            color: #ffffff !important;
                        }
                        body, .fi-main-ctn {
                            background-color: #EEF1F8 !important;
                        }
                        .fi-sidebar-nav {
                            background-color: #ffffff !important;
                        }
                        .fi-logo {
                            max-height: 48px;
                        }
                    </style>
                ')
            )
            ->navigationGroups([
                NavigationGroup::make('Organisation')->collapsible(false),
                NavigationGroup::make('Firm')->collapsible(false),
                NavigationGroup::make('Project')->collapsible(false),
                NavigationGroup::make('Reports')->collapsible(false),
                NavigationGroup::make('Settings')->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make('Add New Project')
                    ->url(fn (): string => ProjectResource::getUrl('create'))
                    ->icon('heroicon-o-plus-circle')
                    ->group('Project')
                    ->sort(2)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.projects.create')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
