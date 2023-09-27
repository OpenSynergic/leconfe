<?php

namespace App\Administration\Resources\StaticPageResource\Pages;

use App\Actions\StaticPages\StaticPageCreateAction;
use App\Administration\Resources\StaticPageResource;
use App\Models\Enums\ContentType;
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
