<?php

use App\Http\Controllers\LogoutController;
use App\Models\Media;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get('view/files/{uuid}', function ($uuid) {
    $media = Media::findByUuid($uuid);

    abort_if(! $media, 404);

    return response()
        ->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="'.$media->file_name.'"',
            'Content-Length' => $media->size,
            'Content-Transfer-Encoding' => 'binary',
            'Accept-Ranges' => 'bytes',
        ]);
})->name('submission-files.view');

Route::get('private/files/{uuid}', function ($uuid, Request $request) {
    $media = Media::findByUuid($uuid);

    abort_if(! $media, 404);

    return response()
        ->download($media->getPath(), $media->file_name, [
            'Content-Type' => $media->mime_type,
            'Content-Length' => $media->size,
        ]);
})->name('private.files');

Route::get('local/temp/{path}', function (string $path, Request $request) {
    abort_if(! $request->hasValidSignature(), 401);

    $storage = Storage::disk('local');

    abort_if(! $storage->exists($path), 404);

    return $storage->download($path);
})->where('path', '.*')->name('local.temp');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('filament.panel.tenant');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('phpmyinfo', function () {
    phpinfo();
})->middleware('admin')->name('phpmyinfo');

Route::get('{conference:path}/logout', LogoutController::class)->name('conference.logout');
Route::post('{conference:path}/logout', LogoutController::class)->name('conference.logout');
Route::get('logout', LogoutController::class)->name('logout');
Route::post('logout', LogoutController::class)->name('logout');