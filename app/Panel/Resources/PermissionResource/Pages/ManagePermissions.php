<?php

namespace App\Panel\Resources\PermissionResource\Pages;

use App\Models\Permission;
use App\Panel\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn (array $data) => Permission::create(['name' => data_get($data, 'context') . ':' . data_get($data, 'action')])),
        ];
    }
}
