<?php

use App\Http\Controllers\PlaylistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [PlaylistController::class, 'index'])->name('playlists.index');
Route::post('/fetch-playlists', [PlaylistController::class, 'fetch'])->name('playlists.fetch');
