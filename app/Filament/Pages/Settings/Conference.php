<?php

namespace App\Filament\Pages\Settings;

use Closure;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;

class Conference extends Page
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';


    public function mount()
    {
    }

    public static function getRoutes(): Closure
    {
        return function () {
            $slug = static::getSlug();

            Route::get('settings/' . $slug, static::class)
                ->middleware(static::getMiddlewares())
                ->name($slug);
        };
    }
}
