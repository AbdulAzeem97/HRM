<?php

namespace App\Http\Controllers;

use App\Models\OvertimeCalculation;
use App\Services\OvertimeCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OvertimeCalculationController extends Controller
{
    protected $overtimeService;

    public function __construct(OvertimeCalculationService $overtimeService)
    {
        $this->overtimeService = $overtimeService;
    }

    /**
     * Display overtime calculations
     */
    public function index(Request $request)
    {
        $query = OvertimeCalculation::with('employee')
                                   ->orderBy('attendance_date', 'desc');

        // Apply filters
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->month && $request->year) {
            $query->whereYear('attendance_date', $request->year)
                  ->whereMonth('attendance_date', $request->month);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $calculations = $query->paginate(50);

        return view('overtime.calculations.index', compact('calculations'));
    }

    /**
     * Show overtime calculation details
     */
    public function show(OvertimeCalculation $overtimeCalculation)
    {
        $overtimeCalculation->load('employee', 'attendance');
        return view('overtime.calculations.show', compact('overtimeCalculation'));
    }

    /**
     * Process overtime for a date range
     */
    public function processRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id'
        ]);

        $result = $this->overtimeService->processOvertimeForDateRange(
            $request->start_date,
            $request->end_date,
            $request->employee_id
        );

        return response()->json([
            'success' => true,
            'message' => "Processed {$result['processed']} overtime calculations",
            'data' => $result
        ]);
    }

    /**
     * Recalculate overtime for a month
     */
    public function recalculateMonth(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'employee_id' => 'nullable|exists:employees,id'
        ]);

        $result = $this->overtimeService->recalculateMonthlyOvertime(
            $request->year,
            $request->month,
            $request->employee_id
        );

        return response()->json([
            'success' => true,
            'message' => "Recalculated overtime for {$request->year}-{$request->month}",
            'data' => $result
        ]);
    }

    /**
     * Get overtime summary for payroll
     */
    public function payrollSummary(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $summary = $this->overtimeService->getOvertimeSummaryForPayroll(
            $request->employee_id,
            $request->year,
            $request->month
        );

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Verify overtime calculations
     */
    public function verify(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $updated = $this->overtimeService->verifyOvertimeCalculations(
            $request->employee_id,
            $request->year,
            $request->month
        );

        return response()->json([
            'success' => true,
            'message' => "Verified {$updated} overtime calculations"
        ]);
    }

    /**
     * Mark overtime as paid
     */
    public function markPaid(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $updated = $this->overtimeService->markOvertimeAsPaid(
            $request->employee_id,
            $request->year,
            $request->month
        );

        return response()->json([
            'success' => true,
            'message' => "Marked {$updated} overtime calculations as paid"
        ]);
    }

    /**
     * Get overtime report
     */
    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        $report = $this->overtimeService->getOvertimeReport(
            $request->start_date,
            $request->end_date,
            $request->department_id,
            $request->company_id
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Auto-process recent overtime
     */
    public function autoProcess(Request $request)
    {
        $daysBack = $request->input('days_back', 7);

        $result = $this->overtimeService->autoProcessRecentOvertime($daysBack);

        return response()->json([
            'success' => true,
            'message' => "Auto-processed overtime for last {$daysBack} days",
            'data' => $result
        ]);
    }

    /**
     * Delete overtime calculation
     */
    public function destroy(OvertimeCalculation $overtimeCalculation)
    {
        $overtimeCalculation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Overtime calculation deleted successfully'
        ]);
    }

    /**
     * Bulk delete overtime calculations
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:overtime_calculations,id'
        ]);

        $deleted = OvertimeCalculation::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} overtime calculations"
        ]);
    }

    /**
     * Export overtime data
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
            'format' => 'in:csv,excel'
        ]);

        $query = OvertimeCalculation::with('employee')
                                   ->whereBetween('attendance_date', [$request->start_date, $request->end_date]);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $calculations = $query->get();

        $data = $calculations->map(function ($calc) {
            return [
                'Employee ID' => $calc->employee_id,
                'Employee Name' => $calc->employee->first_name . ' ' . $calc->employee->last_name,
                'Date' => $calc->attendance_date->format('Y-m-d'),
                'Clock In' => $calc->clock_in,
                'Clock Out' => $calc->clock_out,
                'Working Hours' => $calc->working_hours,
                'Overtime Hours' => $calc->overtime_hours,
                'Overtime Amount' => $calc->overtime_amount,
                'Status' => $calc->status,
                'Shift' => $calc->shift_name
            ];
        });

        $filename = 'overtime_calculations_' . $request->start_date . '_to_' . $request->end_date . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add header row
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
            }

            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}