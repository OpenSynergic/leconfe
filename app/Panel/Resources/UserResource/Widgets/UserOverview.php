<?php

namespace App\Panel\Resources\UserResource\Widgets;

use App\Models\Role;
use App\Models\User;
use App\Panel\Resources\RoleResource;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserOverview extends BaseWidget
{

    protected function getStats(): array
    {
        return [
            Stat::make('Users', $this->getTotalUsers())
                ->extraAttributes([
                    'class' => 'cursor-pointer'
                ]),
            Stat::make('Roles', $this->getTotalRoles())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => 'goToRoles'
                ])
        ];
    }

    public function getTotalUsers()
    {
        return User::count();
    }

    public function getTotalRoles()
    {
        return Role::count();
    }


    public function goToRoles()
    {
        return redirect()->to(RoleResource::getUrl('index'));
    }
}
