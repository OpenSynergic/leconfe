<?php

namespace App\Panel\Administration\Resources\UserResource\Pages;

use App\Panel\Administration\Resources\UserResource;
use App\Panel\Conference\Resources\UserResource\Pages\ListUsers as BaseListUsers;

class ListUsers extends BaseListUsers
{
    protected static string $resource = UserResource::class;
}
