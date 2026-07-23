<?php

namespace App\Panel\Administration\Resources\UserResource\Pages;

use App\Panel\Administration\Resources\UserResource;
use App\Panel\Conference\Resources\UserResource\Pages\EditUser as BaseEditUser;

class EditUser extends BaseEditUser
{
    protected static string $resource = UserResource::class;
}
