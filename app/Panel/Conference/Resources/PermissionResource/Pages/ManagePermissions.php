<?php

namespace App\Panel\Conference\Resources\PermissionResource\Pages;

use App\Actions\Permissions\PermissionPersistAction;
use App\Actions\Roles\RolePersistAssignedPermissions;
use App\Models\Permission;
use App\Panel\Conference\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn (array $data) => Permission::create(['name' => data_get($data, 'context').':'.data_get($data, 'action')])),
            Actions\ActionGroup::make([
                Actions\Action::make('persist')
                    ->requiresConfirmation()
                    ->modalDescription('This will persist all permissions to storage.')
                    ->label('Persist Permissions')
                    ->action(function () {
                        PermissionPersistAction::run();
                    }),
            ]),

        ];
    }
}
