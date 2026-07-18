<?php

namespace App\Panel\Administration\Resources;

use App\Panel\Administration\Resources\UserResource\Pages\ListUsers;
use App\Panel\Administration\Resources\UserResource\Pages\CreateUser;
use App\Panel\Administration\Resources\UserResource\Pages\EditUser;
use App\Panel\Administration\Resources\UserResource\Pages;
use App\Panel\Conference\Resources\UserResource as ConferenceUserResource;
use Filament\Schemas\Schema;

class UserResource extends ConferenceUserResource
{
    public static function form(Schema $schema): Schema
    {
        $schema = parent::form($schema);

        $components = $schema->getComponents();

        if (isset($components[0])) {
            $components[0]->columnSpan(['lg' => 2]);
        }

        if (isset($components[1])) {
            $components[1]
                ->columnSpan(['lg' => 1])
                ->hidden(false);
        }

        $schema->schema($components);

        return $schema;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
