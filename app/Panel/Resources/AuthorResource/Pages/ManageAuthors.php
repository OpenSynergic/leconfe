<?php

namespace App\Panel\Resources\AuthorResource\Pages;

use App\Panel\Resources\AuthorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuthors extends ManageRecords
{

    protected static string $view = 'panel.resources.conferences.author-resource.pages.list-authors';

    protected static string $resource = AuthorResource::class;
}
