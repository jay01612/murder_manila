<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//booking
Route::post('SaveClient', [bookingController::class, 'clientInfoSave']);
Route::post('booking', [ bookingController::class, 'bookingInfoSave']);
Route::post('Availability', [bookingController::class, 'checkIfAvailable']);
Route::get('themes', [bookingController::class, 'showTheme']);

