<?php

namespace App\Panel\ScheduledConference\Resources\UserResource\Pages;

use App\Panel\ScheduledConference\Resources\UserResource;
use App\Panel\Conference\Resources\UserResource\Pages\CreateUser as BaseCreateUser;

class CreateUser extends BaseCreateUser
{
    protected static string $resource = UserResource::class;
}
