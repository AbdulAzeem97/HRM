<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoAutoUpdateController;
use App\Http\Controllers\AttendanceController;


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


Route::get('is-update-available', [DemoAutoUpdateController::class, 'isUpdateAvailable'])->name('is-update-available');

// Enhanced Attendance API Routes
Route::prefix('attendance')->group(function () {
    Route::post('process-biometric', [AttendanceController::class, 'processBiometricAttendance']);
    Route::get('test-shift-detection', [AttendanceController::class, 'testShiftDetection']);
    Route::get('daily-report', [AttendanceController::class, 'getDailyAttendanceReport']);
    Route::get('extra-ot-report', [AttendanceController::class, 'getExtraOTReport']);
    Route::get('summary', [AttendanceController::class, 'getAttendanceSummary']);
});

