<?php

namespace App\Panel\Conference\Resources\Conferences\AuthorRoleResource\Pages;

use App\Panel\Conference\Resources\Conferences\AuthorRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuthorRoles extends ManageRecords
{
    protected static string $resource = AuthorRoleResource::class;
}
