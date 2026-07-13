<?php

namespace App\Panel\Administration\Resources;

use App\Panel\Administration\Resources\UserResource\Pages;
use App\Panel\Conference\Resources\UserResource as ConferenceUserResource;

class UserResource extends ConferenceUserResource
{
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
