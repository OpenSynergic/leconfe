<?php

namespace App\Panel\ScheduledConference\Resources\UserResource\Pages;

use App\Panel\ScheduledConference\Resources\UserResource;
use App\Panel\Conference\Resources\UserResource\Pages\EditUser as BaseEditUser;

class EditUser extends BaseEditUser
{
    protected static string $resource = UserResource::class;
}
