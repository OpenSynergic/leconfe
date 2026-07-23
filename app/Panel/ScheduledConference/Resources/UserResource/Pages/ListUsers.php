<?php

namespace App\Panel\ScheduledConference\Resources\UserResource\Pages;

use App\Panel\ScheduledConference\Resources\UserResource;
use App\Panel\Conference\Resources\UserResource\Pages\ListUsers as BaseListUsers;

class ListUsers extends BaseListUsers
{
    protected static string $resource = UserResource::class;
}
