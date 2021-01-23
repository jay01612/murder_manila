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

Route::post('booking', [ bookingController::class, 'bookingInfoSave']);
Route::post('Availability', [bookingController::class, 'checkAvailability']);
Route::get('themes', [bookingController::class, 'showTheme']);
Route::get('themeValue', [bookingController::class, 'getThemeValue']);

Route::post('generateCode', [bookingController::class, 'createCode']);
Route::post('codes', [bookingController::class, 'verifyClient']);
Route::get('getVerificationCode', [bookingController::class, 'getVerifCode']);
Route::get('sendVerification', [bookingController::class, 'sendVerificationNumber']);
Route::post('sendEmailBillling', [bookingController::class, 'sendBilling']); 
Route::get('reciept', [bookingController::class, 'showReciept']);


//admin login
Route::post('logIn', [adminControllers::class, 'logIn']);
Route::get('positions', [adminControllers::class, 'showPositions']);
Route::get('positionName', [adminControllers::class, 'showPositionName']);
Route::get('logout', [adminControllers::class, 'logout']);

Route::middleware('auth:api')->get('user', function (Request $request){
    return $request->user();
});

//admin 
Route::post('adminRegister', [adminControllers::class, 'registerAdmin'])->middleware('auth:api');
Route::post('AdminDelete', [adminControllers::class, 'deleteAdmin'])->middleware('auth:api');
Route::get('AdminList', [adminControllers::class, 'adminList']);

Route::post('dailyBookings', [adminControllers::class, 'getDailyBookings']);
Route::get('Pendings', [adminControllers::class, 'getPendBookings']);
Route::get('Booked', [adminControllers::class, 'getPaidBooking']);
Route::get('CancelledBookings', [adminControllers::class, 'getCanceledBookings']);//->middleware('auth:api');

Route::put('editpending/{id}', [adminControllers::class, 'bookingEdit']);
Route::put('editCancelBooking/{id}', [adminControllers::class, 'cancelBookingEdit']);

Route::post('initialPaymentEmail', [adminControllers::class, 'sendRecievedHalfPaymentEmail'])->middleware('auth:api');
Route::post('fullPaymentEmail', [adminControllers::class, 'sendRecievedFullPaymentEmail'])->middleware('auth:api');
Route::post('cancelBookingEmail', [adminControllers::class, 'cancelledBookingEmail'])->middleware('auth:api');

