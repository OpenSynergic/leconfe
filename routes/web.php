<?php

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

Route::get('local/temp/{path}', function (string $path, Request $request) {
    abort_if(! $request->hasValidSignature(), 401);

    $storage = Storage::disk('local');

    abort_if(! $storage->exists($path), 404);

    return $storage->download($path);
})
    ->where('path', '.*')
    ->name('local.temp');
