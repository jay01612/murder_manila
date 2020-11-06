<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bookingController;
use App\Http\Controllers\adminControllers;

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
Route::post('Availability', [bookingController::class, 'checkAvailableTime']);
Route::get('themes', [bookingController::class, 'showTheme']);
Route::get('bookingSummary/{id}', [bookingController::class, 'bookingSummary']);
Route::post('Billing', [bookingController::class, 'bookingAmount']);
Route::get('AmountSummary/{id}', [bookingController::class, 'amountBookingSummary']);


//admin side
Route::post('login', [adminControllers::class, 'logIn']);
Route::get('positions', [adminControllers::class, 'showPositions']);
Route::post('adminRegister', [adminControllers::class, 'registerAdmin'])->middleware('auth:api');
Route::post('AdminDelete', [adminControllers::class, 'deleteAdmin'])->middleware('auth:api');
Route::get('Pendings', [adminControllers::class, 'getPendBookings'])->middleware('auth:api');
Route::get('Booked', [adminControllers::class, 'getPaidBooking'])->middleware('auth:api');


