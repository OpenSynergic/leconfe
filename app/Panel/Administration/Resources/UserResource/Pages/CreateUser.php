<?php

namespace App\Panel\Administration\Resources\UserResource\Pages;

use App\Panel\Administration\Resources\UserResource;
use App\Panel\Conference\Resources\UserResource\Pages\CreateUser as BaseCreateUser;

class CreateUser extends BaseCreateUser
{
    protected static string $resource = UserResource::class;
}
