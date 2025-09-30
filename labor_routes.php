<?php
// Add these routes to your routes/web.php file

use App\Http\Controllers\LaborEmployeeController;

// Labor Employee Management Routes
Route::prefix('labor')->name('labor.')->middleware(['auth'])->group(function () {
    Route::get('/', [LaborEmployeeController::class, 'index'])->name('index');
    Route::get('/create', [LaborEmployeeController::class, 'create'])->name('create');
    Route::post('/store', [LaborEmployeeController::class, 'store'])->name('store');
    Route::delete('/destroy', [LaborEmployeeController::class, 'destroy'])->name('destroy');
    Route::post('/remove-shifts', [LaborEmployeeController::class, 'removeShifts'])->name('remove-shifts');
    Route::post('/process-attendance', [LaborEmployeeController::class, 'processAttendance'])->name('process-attendance');
    Route::get('/attendance', [LaborEmployeeController::class, 'attendancePage'])->name('attendance');
});

// API Routes for Labor Management
Route::prefix('api/labor')->middleware(['auth'])->group(function () {
    Route::get('/stats', [LaborEmployeeController::class, 'stats'])->name('api.labor.stats');
    Route::get('/employees', [LaborEmployeeController::class, 'laborEmployees'])->name('api.labor.employees');
    Route::post('/mark-department', [LaborEmployeeController::class, 'bulkMarkByDepartment'])->name('api.labor.mark-department');
    Route::post('/mark-designation', [LaborEmployeeController::class, 'bulkMarkByDesignation'])->name('api.labor.mark-designation');
});

?>