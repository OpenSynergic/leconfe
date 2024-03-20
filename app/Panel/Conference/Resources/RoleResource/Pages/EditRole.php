<?php

namespace App\Panel\Conference\Resources\RoleResource\Pages;

use App\Actions\Roles\RoleUpdateAction;
use App\Panel\Conference\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\PermissionRegistrar;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['permissions'] = collect(data_get($data, 'permissions', []))
            ->filter(fn (bool $value) => $value)
            ->keys()
            ->toArray();

        return RoleUpdateAction::run($record, $data);
    }
}
