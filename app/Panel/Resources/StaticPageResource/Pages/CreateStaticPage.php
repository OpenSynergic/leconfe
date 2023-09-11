<?php

namespace App\Panel\Resources\StaticPageResource\Pages;

use App\Actions\StaticPages\StaticPageCreateAction;
use App\Models\Enums\ContentType;
use App\Models\StaticPage;
use App\Panel\Resources\StaticPageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateStaticPage extends CreateRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $path = Str::slug($data['title']);
        $staticPage = StaticPage::WhereMeta('path', $path)->first();

        if ($staticPage) {
            $path .= '-1';
        }

        $data['content_type'] = ContentType::StaticPage;
        $data['path'] = $path;

        return StaticPageCreateAction::run($data);
    }
}
