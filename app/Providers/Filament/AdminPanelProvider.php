<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ProjectResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Resources\RoleResource as ShieldRoleResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
            ->brandName(fn (): string => 'ePPMS')
            ->brandLogo(fn (): ?string => null)
            ->brandLogoHeight('48px')
            ->colors([
                'primary' => Color::hex('#5B5FC7'),
                'gray' => Color::Gray,
            ])
            ->darkMode()
            ->sidebarCollapsibleOnDesktop(false)
            ->sidebarFullyCollapsibleOnDesktop(false)
            ->collapsibleNavigationGroups(true)
            ->topNavigation(false)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('
                    <style>
                        /* Top navigation bar */
                        .fi-topbar {
                            background-color: #0D1B3E !important;
                            display: block !important;
                            height: auto !important;
                            min-height: 4rem !important;
                            visibility: visible !important;
                            opacity: 1 !important;
                        }
                        header.fi-topbar,
                        div.fi-topbar,
                        nav.fi-topbar,
                        [class*="fi-topbar"] {
                            background-color: #0D1B3E !important;
                        }
                        .fi-topbar nav,
                        .fi-topbar .fi-topbar-breadcrumbs,
                        .fi-topbar button,
                        .fi-topbar svg,
                        .fi-topbar span,
                        .fi-topbar a {
                            color: #ffffff !important;
                        }
                        .fi-user-avatar-wrapper img {
                            border: 2px solid #ffffff;
                        }
                        .eppms-topbar-brand {
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            min-width: 0;
                        }
                        .eppms-topbar-brand img {
                            height: 2.5rem;
                            width: auto;
                            max-width: 9rem;
                            object-fit: contain;
                        }
                        .eppms-topbar-brand span {
                            color: #ffffff !important;
                            font-size: 1.125rem;
                            font-weight: 800;
                            letter-spacing: 0.01em;
                            white-space: nowrap;
                        }

                        /* Sidebar */
                        .fi-sidebar {
                            background-color: #ffffff !important;
                            border-right: 1px solid #e5e7eb;
                            width: var(--sidebar-width) !important;
                            min-width: var(--sidebar-width) !important;
                            visibility: visible !important;
                        }
                        @media (min-width: 1024px) {
                            .fi-sidebar {
                                transform: translateX(0) !important;
                            }
                            [dir="rtl"] .fi-sidebar {
                                transform: translateX(0) !important;
                            }
                            .fi-topbar-open-sidebar-btn,
                            .fi-topbar-close-sidebar-btn {
                                display: none !important;
                            }
                        }
                        .fi-sidebar-nav,
                        .fi-sidebar-nav-groups {
                            background-color: #ffffff !important;
                        }

                        /* Page background */
                        .fi-main,
                        .fi-main-ctn,
                        body {
                            background-color: #EEF1F8 !important;
                        }

                        /* Dashboard widget cards */
                        .fi-wi-stats-overview-stat,
                        .fi-widget {
                            background-color: #ffffff !important;
                            border-radius: 12px !important;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
                        }
                        .eppms-dashboard-card {
                            background-color: #ffffff !important;
                            border-radius: 12px !important;
                            padding: 24px !important;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
                        }

                        /* Stat card colours */
                        .stat-departments {
                            background-color: #E6F7F5 !important;
                        }
                        .stat-units {
                            background-color: #FCE8EC !important;
                        }
                        .stat-personnels {
                            background-color: #E8F5EE !important;
                        }

                        /* Active nav item */
                        .fi-sidebar-item-active .fi-sidebar-item-button {
                            background-color: #5B5FC7 !important;
                            color: #ffffff !important;
                            border-radius: 8px !important;
                        }
                        .fi-sidebar-item-active .fi-sidebar-item-label,
                        .fi-sidebar-item-active svg {
                            color: #ffffff !important;
                        }

                        /* Primary buttons */
                        .fi-btn-primary {
                            background-color: #5B5FC7 !important;
                        }

                        /* Section headings */
                        .fi-section-header-heading {
                            color: #0D1B3E !important;
                            font-weight: 700 !important;
                        }
                        .fi-logo {
                            max-height: 48px;
                        }

                        /* Dark mode overrides */
                        .dark .fi-sidebar,
                        .dark .fi-sidebar-nav,
                        .dark .fi-sidebar-nav-groups {
                            background-color: #1e2433 !important;
                        }
                        .dark .fi-main,
                        .dark .fi-main-ctn,
                        .dark body {
                            background-color: #111827 !important;
                        }
                        .dark .fi-wi-stats-overview-stat,
                        .dark .fi-widget,
                        .dark .eppms-dashboard-card {
                            background-color: #1e2433 !important;
                        }
                        .dark .fi-topbar {
                            background-color: #0a1128 !important;
                        }
                        .dark .stat-departments {
                            background-color: #0d3330 !important;
                        }
                        .dark .stat-units {
                            background-color: #3d1a20 !important;
                        }
                        .dark .stat-personnels {
                            background-color: #0d2e1a !important;
                        }
                        .dark .eppms-dashboard-card h1,
                        .dark .eppms-dashboard-card h2,
                        .dark .eppms-dashboard-card h3,
                        .dark .eppms-dashboard-card .text-gray-950,
                        .dark .eppms-dashboard-card .text-gray-900 {
                            color: #f9fafb !important;
                        }
                        .dark .eppms-dashboard-card .text-gray-700,
                        .dark .eppms-dashboard-card .text-gray-600,
                        .dark .eppms-dashboard-card .text-gray-500 {
                            color: #d1d5db !important;
                        }
                        .dark .eppms-dashboard-card .bg-white {
                            background-color: #273044 !important;
                        }

                        /* ─── LIGHT MODE — improve contrast ─── */

                        /* Ensure all table text is clearly visible */
                        .fi-ta-text, .fi-ta-cell, td, th {
                            color: #111827 !important;
                        }

                        /* Table header background */
                        .fi-ta-header-cell {
                            background-color: #f3f4f6 !important;
                            color: #111827 !important;
                            font-weight: 600 !important;
                        }

                        /* Table row hover */
                        .fi-ta-row:hover td {
                            background-color: #f9fafb !important;
                        }

                        /* Sidebar text visibility */
                        .fi-sidebar-item-label {
                            color: #374151 !important;
                        }

                        /* Navigation group labels */
                        .fi-sidebar-group-label {
                            color: #6b7280 !important;
                            font-weight: 600 !important;
                            font-size: 11px !important;
                            text-transform: uppercase !important;
                            letter-spacing: 0.05em !important;
                        }

                        /* Card/widget backgrounds */
                        .fi-wi-stats-overview-stat-value {
                            color: #111827 !important;
                            font-weight: 700 !important;
                        }
                        .fi-wi-stats-overview-stat-label {
                            color: #6b7280 !important;
                        }

                        /* Form labels */
                        .fi-fo-field-wrp-label label {
                            color: #374151 !important;
                            font-weight: 500 !important;
                        }

                        /* Input fields */
                        input, select, textarea {
                            color: #111827 !important;
                            background-color: #ffffff !important;
                            border-color: #d1d5db !important;
                        }

                        /* Buttons text */
                        .fi-btn {
                            font-weight: 500 !important;
                        }

                        /* Page headings */
                        .fi-header-heading,
                        h1, h2, h3 {
                            color: #111827 !important;
                        }

                        /* Breadcrumb */
                        .fi-breadcrumbs-item-label {
                            color: #6b7280 !important;
                        }

                        /* Stats bar custom cards */
                        [style*="background:#ffffff"] div,
                        [style*="background: #ffffff"] div {
                            color: #111827 !important;
                        }

                        /* Badge/pill text */
                        .fi-badge {
                            font-weight: 500 !important;
                        }

                        /* ─── DARK MODE — ensure everything readable ─── */

                        .dark .fi-sidebar-item-label {
                            color: #e5e7eb !important;
                        }

                        .dark .fi-sidebar-group-label {
                            color: #9ca3af !important;
                        }

                        .dark .fi-ta-text,
                        .dark .fi-ta-cell,
                        .dark td,
                        .dark th {
                            color: #f3f4f6 !important;
                        }

                        .dark .fi-ta-header-cell {
                            background-color: #1f2937 !important;
                            color: #f3f4f6 !important;
                        }

                        .dark .fi-ta-row:hover td {
                            background-color: #1f2937 !important;
                        }

                        .dark input,
                        .dark select,
                        .dark textarea {
                            color: #f3f4f6 !important;
                            background-color: #1f2937 !important;
                            border-color: #374151 !important;
                        }

                        .dark .fi-fo-field-wrp-label label {
                            color: #e5e7eb !important;
                        }

                        .dark .fi-header-heading,
                        .dark h1, .dark h2, .dark h3 {
                            color: #f9fafb !important;
                        }

                        .dark .fi-breadcrumbs-item-label {
                            color: #9ca3af !important;
                        }

                        .dark .fi-wi-stats-overview-stat-value {
                            color: #f9fafb !important;
                        }

                        .dark .fi-wi-stats-overview-stat-label {
                            color: #9ca3af !important;
                        }

                        /* Dark mode stat cards */
                        .dark [style*="background:#E6F7F5"] {
                            background-color: #0d3330 !important;
                            color: #a7f3d0 !important;
                        }
                        .dark [style*="background:#FCE8EC"] {
                            background-color: #3d1a20 !important;
                            color: #fca5a5 !important;
                        }
                        .dark [style*="background:#E8F5EE"] {
                            background-color: #0d2e1a !important;
                            color: #86efac !important;
                        }
                        .dark [style*="background:#ffffff"],
                        .dark [style*="background: #ffffff"],
                        .dark [style*="background:#FFFFFF"] {
                            background-color: #1e2433 !important;
                            color: #f9fafb !important;
                        }

                        /* Dark mode inline styles in blade views */
                        .dark [style*="color:#111827"] {
                            color: #f9fafb !important;
                        }
                        .dark [style*="color:#374151"] {
                            color: #e5e7eb !important;
                        }
                        .dark [style*="color:#6b7280"] {
                            color: #9ca3af !important;
                        }
                        .dark [style*="background:#f7f7f7"] {
                            background-color: #1f2937 !important;
                        }
                        .dark [style*="background:#f3f4f6"] {
                            background-color: #1f2937 !important;
                        }

                        /* Dark mode page backgrounds */
                        .dark .fi-main,
                        .dark .fi-main-ctn,
                        .dark body {
                            background-color: #111827 !important;
                        }

                        /* Dark mode cards */
                        .dark .fi-section,
                        .dark .fi-wi,
                        .dark .fi-card {
                            background-color: #1e2433 !important;
                            border-color: #374151 !important;
                        }

                        /* Dark mode table */
                        .dark .fi-ta-table {
                            background-color: #1e2433 !important;
                        }
                        .dark .fi-ta-wrap {
                            background-color: #1e2433 !important;
                        }

                        /* Dark mode notification bell and search */
                        .dark .fi-topbar-search-btn,
                        .dark .fi-notifications-trigger {
                            color: #ffffff !important;
                        }

                        /* Dark mode select dropdown options */
                        .dark option {
                            background-color: #1f2937 !important;
                            color: #f3f4f6 !important;
                        }

                        /* Dark mode pagination */
                        .dark .fi-pagination-item {
                            background-color: #1f2937 !important;
                            border-color: #374151 !important;
                            color: #e5e7eb !important;
                        }

                        /* Dark mode modal/dialog */
                        .dark .fi-modal-window {
                            background-color: #1e2433 !important;
                            color: #f3f4f6 !important;
                        }

                        /* ─── TRANSITION for smooth theme switching ─── */
                        *, *::before, *::after {
                            transition: background-color 0.2s ease,
                                        color 0.2s ease,
                                        border-color 0.2s ease !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => Blade::render('
                    <a href="{{ url(\'/admin\') }}" class="eppms-topbar-brand">
                        <span>ePPMS</span>
                    </a>
                ')
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => Blade::render('<div class="ms-2 flex items-center rounded-lg bg-white/10 px-2 py-1"><x-filament-panels::theme-switcher /></div>')
            )
            ->navigationGroups([
                'Organisation',
                'Firm',
                'Project',
                'Reports',
                'Settings',
            ])
            ->navigationItems([
                NavigationItem::make('Add New Project')
                    ->url(fn (): string => ProjectResource::getUrl('create'))
                    ->icon('heroicon-o-plus-circle')
                    ->group('Project')
                    ->sort(2)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.projects.create')),
                NavigationItem::make('Roles')
                    ->url(fn (): string => ShieldRoleResource::getUrl('index'))
                    ->icon('heroicon-o-shield-check')
                    ->group('Settings')
                    ->sort(1)
                    ->visible(fn (): bool => ShieldRoleResource::canViewAny())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.shield.roles.*')),
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
