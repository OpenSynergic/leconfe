<?php

namespace App\Panel\Conference\Resources\Conferences\AuthorResource\Pages;

use App\Panel\Conference\Resources\Conferences\AuthorResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAuthors extends ManageRecords
{
    protected static string $view = 'panel.conference.resources.conferences.author-resource.pages.list-authors';

    protected static string $resource = AuthorResource::class;
}
