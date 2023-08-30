<?php

namespace App\Panel\Resources\RoleResource\Pages;

use App\Actions\Roles\RoleCreateAction;
use App\Panel\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $data['permissions'] = collect(data_get($data, 'permissions', []))
            ->filter(fn (bool $value) => $value)
            ->keys()
            ->toArray();

       return RoleCreateAction::run($data);
    }
}