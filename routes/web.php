<?php

use App\Models\Conference;
use App\Models\StaticPage;
use Spatie\Sitemap\Sitemap;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/sitemap', function () {
    return Sitemap::create()
        ->add('/')
        ->add('/about')
        ->add('/contact')
        ->add('/current')
        ->add('/register')
        ->add('/login')
        ->add('/current/announcements')
        ->add(Announcement::with(['conference'])->get())
        ->add(StaticPage::with(['conference'])->get());
})->name('generate-sitemap');

Route::get('local/temp/{path}', function (string $path, Request $request) {
    abort_if(!$request->hasValidSignature(), 401);

    $storage = Storage::disk('local');

    abort_if(!$storage->exists($path), 404);

    return $storage->download($path);
})->where('path', '.*')->name('local.temp');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('filament.panel.tenant');
})->middleware(['auth', 'signed'])->name('verification.verify');


Route::get('/test', function () {
    dd($clonedDataConference = Conference::with(['topics', 'meta', 'navigations'])->findOrFail(354817923763507)->replicate());
});
