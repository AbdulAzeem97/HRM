<?php

/**
 * Overtime Calculations Routes
 * Add these routes to your web.php file
 */

// Overtime Calculations Routes
Route::prefix('overtime-calculations')->name('overtime.calculations.')->group(function () {
    Route::get('/', [App\Http\Controllers\OvertimeCalculationController::class, 'index'])->name('index');
    Route::get('/{overtimeCalculation}', [App\Http\Controllers\OvertimeCalculationController::class, 'show'])->name('show');
    Route::delete('/{overtimeCalculation}', [App\Http\Controllers\OvertimeCalculationController::class, 'destroy'])->name('destroy');

    // API endpoints for processing
    Route::post('/process-range', [App\Http\Controllers\OvertimeCalculationController::class, 'processRange'])->name('process.range');
    Route::post('/recalculate-month', [App\Http\Controllers\OvertimeCalculationController::class, 'recalculateMonth'])->name('recalculate.month');
    Route::post('/verify', [App\Http\Controllers\OvertimeCalculationController::class, 'verify'])->name('verify');
    Route::post('/mark-paid', [App\Http\Controllers\OvertimeCalculationController::class, 'markPaid'])->name('mark.paid');
    Route::post('/auto-process', [App\Http\Controllers\OvertimeCalculationController::class, 'autoProcess'])->name('auto.process');
    Route::post('/bulk-delete', [App\Http\Controllers\OvertimeCalculationController::class, 'bulkDelete'])->name('bulk.delete');

    // Reports and exports
    Route::get('/payroll-summary', [App\Http\Controllers\OvertimeCalculationController::class, 'payrollSummary'])->name('payroll.summary');
    Route::get('/report', [App\Http\Controllers\OvertimeCalculationController::class, 'report'])->name('report');
    Route::get('/export', [App\Http\Controllers\OvertimeCalculationController::class, 'export'])->name('export');
});

/**
 * Add these to your existing routes or create new menu items
 */

// In your admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('overtime-calculations', App\Http\Controllers\OvertimeCalculationController::class);
});

// In your HR routes
Route::middleware(['auth', 'hr'])->group(function () {
    Route::get('overtime-calculations', [App\Http\Controllers\OvertimeCalculationController::class, 'index']);
    Route::get('overtime-calculations/report', [App\Http\Controllers\OvertimeCalculationController::class, 'report']);
});

/**
 * API Routes for AJAX calls
 */
Route::prefix('api/overtime')->middleware(['auth'])->group(function () {
    Route::get('/employee/{employeeId}/month/{year}/{month}', function($employeeId, $year, $month) {
        $service = app(\App\Services\OvertimeCalculationService::class);
        return response()->json($service->getOvertimeSummaryForPayroll($employeeId, $year, $month));
    });

    Route::post('/process/attendance/{attendanceId}', function($attendanceId) {
        $attendance = \App\Models\Attendance::findOrFail($attendanceId);
        $service = app(\App\Services\OvertimeCalculationService::class);
        $result = $service->processAttendanceOvertime($attendance);
        return response()->json(['success' => true, 'data' => $result]);
    });
});

/**
 * Menu Integration
 * Add this to your sidebar/navigation
 */
?>

<!-- Menu Item for Overtime Calculations -->
<li class="nav-item">
    <a href="{{ route('overtime.calculations.index') }}" class="nav-link">
        <i class="nav-icon fas fa-clock"></i>
        <p>Overtime Calculations</p>
    </a>
</li>

<!-- Submenu for Overtime Management -->
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-business-time"></i>
        <p>
            Overtime Management
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('overtime.calculations.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Calculations</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('overtime.calculations.report') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Reports</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('overtime.calculations.export') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Export Data</p>
            </a>
        </li>
    </ul>
</li>