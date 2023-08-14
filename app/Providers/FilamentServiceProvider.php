<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        Filament::serving(function () {

            $this->setupAssets();
            $this->setupFileUploads();
            $this->setupFormat();
        });
    }

    protected function setupAssets()
    {
        Filament::registerRenderHook(
            'panels::scripts.before',
            fn () => Blade::render(<<<Blade
                    @vite(['resources/js/app.js'])
                Blade)
        );
    }

    protected function setupFileUploads()
    {
        // TODO Validasi file type menggunakan dengan menggunakan format extension, bukan dengan mime type, hal ini agar mempermudah pengguna dalam melakukan setting file apa saja yang diperbolehkan
        // Saat ini SpatieMediaLibraryFileUpload hanya support file validation dengan mime type.
        // Solusi mungkin buat custom component upload dengan menggunakan library seperti dropzone, atau yang lainnya.
        SpatieMediaLibraryFileUpload::configureUsing(function (SpatieMediaLibraryFileUpload $fileUpload): void {
            $fileUpload->maxSize(config('media-library.max_file_size') / 1024)
                // ->acceptedFileTypes(config('media-library.accepted_file_types'))
            ;
        });
    }

    protected function setupFormat()
    {
        DatePicker::configureUsing(function (DatePicker $datePicker): void {
            $datePicker->displayFormat(setting('format.date'));
        });

        TimePicker::configureUsing(function (TimePicker $timePicker): void {
            $timePicker->displayFormat(setting('format.time'));
        });
    }
}
