<?php

namespace App\Panel\Conference\Resources\RoleResource\Pages;

use App\Panel\Conference\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Spatie\Permission\PermissionRegistrar;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),

        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = app(PermissionRegistrar::class)
            ->getPermissions()
            ->mapWithKeys(fn ($permission) => [$permission->name => ! $this->getRecord()->ancestorsAndSelf->pluck('id')->intersect($permission->roles->pluck('id')->toArray())->isEmpty()])
            ->toArray();

        return $data;
    }
}
