<?php

namespace App\Panel\Administration\Resources;

use App\Panel\Administration\Resources\UserResource\Pages\ListUsers;
use App\Panel\Administration\Resources\UserResource\Pages\CreateUser;
use App\Panel\Administration\Resources\UserResource\Pages\EditUser;
use App\Panel\Administration\Resources\UserResource\Pages;
use App\Panel\Conference\Resources\UserResource as ConferenceUserResource;

class UserResource extends ConferenceUserResource
{
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
