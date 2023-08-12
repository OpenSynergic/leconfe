<?php

namespace App\Panel\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Livewire\Panel\Tables\RolesTable;
use App\Livewire\Panel\Tables\UsersTable;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class UserRoles extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'panel.pages.settings.user-roles';

    protected static ?string $title = 'Users & Roles';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Label')
                    ->tabs([
                        Tabs\Tab::make('Users')
                            ->icon('heroicon-o-users')
                            ->schema([
                                LivewireEntry::make('users', UsersTable::class)
                                    ->lazy(),
                            ]),
                        Tabs\Tab::make('Roles')
                            ->icon('heroicon-o-shield-check')
                            ->schema([

                                LivewireEntry::make('roles', RolesTable::class)
                                    ->lazy(),
                            ]),
                        Tabs\Tab::make('Access Options')
                            ->icon('heroicon-o-key')
                            ->schema([]),
                    ])
                    ->contained(false)
                    ->persistTabInQueryString(),
            ]);
    }
}
