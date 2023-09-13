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
        $slug = Str::slug($data['title']);
        $currentSlug = Str::slug($data['title']);
        $count = 1;
        
        while (true) {
            $staticPage = StaticPage::where('slug', $currentSlug)->first();
            if ($staticPage) {
                $currentSlug = "{$slug}-{$count}";
            } else {
                break;
            }
        }

        $data['content_type'] = ContentType::StaticPage;
        $data['slug'] = $currentSlug;

        return StaticPageCreateAction::run($data);
    }
}
