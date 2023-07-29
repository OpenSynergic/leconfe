<?php

namespace App\Filament\Pages\Settings;

use App\Models\User;
use Filament\Pages\Page;
use Spatie\Tags\Tag;

class UserRoles extends Page
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.settings.user-roles';

    protected static ?string $title = 'Users & Roles';
}
