<?php

namespace App\Panel\Resources\StaticPageResource\Pages;

use App\Actions\StaticPages\StaticPageCreateAction;
use App\Models\Enums\ContentType;
use App\Panel\Resources\StaticPageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStaticPage extends CreateRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['content_type'] = ContentType::StaticPage;

        return StaticPageCreateAction::run($data);
    }
}
