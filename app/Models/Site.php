<?php

namespace App\Models;

use App\Models\Meta\SiteMeta;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Site extends Model implements HasMedia
{
    use Cachable, InteractsWithMedia, Metable;

    protected $table = 'site';

    public static function getSite()
    {
        return Site::query()->first();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('tenant')
            ->keepOriginalImageFormat()
            ->width(50);

        $this->addMediaConversion('thumb')
            ->keepOriginalImageFormat()
            ->width(400);

        $this->addMediaConversion('thumb-xl')
            ->keepOriginalImageFormat()
            ->width(800);
    }

    protected function getMetaClassName(): string
    {
        return SiteMeta::class;
    }

    protected function getAllDefaultMeta(): array
    {
        return [
            'settings_allow_registration' => true,
            'settings_must_verify_email' => false,
            'settings_select_format_date' => 'j F Y',
            'settings_format_date' => 'j F Y',
            'settings_select_format_time' => 'H:i',
            'settings_format_time' => 'H:i',
            'page_footer' => view('frontend.examples.footer')->render(),
        ];
    }
}
