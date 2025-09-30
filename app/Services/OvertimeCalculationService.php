<?php

namespace App\Services;

use App\Models\OvertimeCalculation;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OvertimeCalculationService
{
    /**
     * Process overtime for a single attendance record
     */
    public function processAttendanceOvertime(Attendance $attendance)
    {
        $employee = Employee::with('officeShift')->find($attendance->employee_id);

        if (!$employee || !$employee->overtime_allowed) {
            Log::info("Skipping overtime calculation for employee {$attendance->employee_id} - not eligible");
            return null;
        }

        return OvertimeCalculation::calculateAndStore($attendance, $employee);
    }

    /**
     * Process overtime for all attendances in a date range
     */
    public function processOvertimeForDateRange($startDate, $endDate, $employeeId = null)
    {
        $query = Attendance::whereBetween('attendance_date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->get();
        $processed = 0;
        $errors = 0;

        foreach ($attendances as $attendance) {
            try {
                $result = $this->processAttendanceOvertime($attendance);
                if ($result) {
                    $processed++;
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error("Error processing overtime for attendance {$attendance->id}: " . $e->getMessage());
            }
        }

        return [
            'total_attendances' => $attendances->count(),
            'processed' => $processed,
            'errors' => $errors
        ];
    }

    /**
     * Recalculate overtime for a specific month
     */
    public function recalculateMonthlyOvertime($year, $month, $employeeId = null)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Delete existing calculations for the period
        $deleteQuery = OvertimeCalculation::whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($employeeId) {
            $deleteQuery->where('employee_id', $employeeId);
        }

        $deletedCount = $deleteQuery->count();
        $deleteQuery->delete();

        // Recalculate
        $result = $this->processOvertimeForDateRange($startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $employeeId);
        $result['deleted_old_records'] = $deletedCount;

        return $result;
    }

    /**
     * Get overtime summary for payroll
     */
    public function getOvertimeSummaryForPayroll($employeeId, $year, $month)
    {
        $calculations = OvertimeCalculation::forEmployee($employeeId)
                                          ->forMonth($year, $month)
                                          ->withOvertime()
                                          ->get();

        $totalAmount = $calculations->sum('overtime_amount');
        $totalHours = $calculations->sum('net_overtime_minutes') / 60;
        $totalDays = $calculations->count();

        return [
            'employee_id' => $employeeId,
            'year' => $year,
            'month' => $month,
            'total_overtime_amount' => round($totalAmount, 2),
            'total_overtime_hours' => round($totalHours, 2),
            'overtime_days' => $totalDays,
            'calculations' => $calculations->map(function ($calc) {
                return [
                    'date' => $calc->attendance_date->format('Y-m-d'),
                    'overtime_hours' => $calc->overtime_hours,
                    'overtime_amount' => $calc->overtime_amount,
                    'shift_name' => $calc->shift_name,
                    'status' => $calc->status
                ];
            })
        ];
    }

    /**
     * Verify overtime calculations
     */
    public function verifyOvertimeCalculations($employeeId, $year, $month)
    {
        $updated = OvertimeCalculation::forEmployee($employeeId)
                                     ->forMonth($year, $month)
                                     ->where('status', 'calculated')
                                     ->update(['status' => 'verified']);

        return $updated;
    }

    /**
     * Mark overtime as paid
     */
    public function markOvertimeAsPaid($employeeId, $year, $month)
    {
        $updated = OvertimeCalculation::forEmployee($employeeId)
                                     ->forMonth($year, $month)
                                     ->whereIn('status', ['calculated', 'verified'])
                                     ->update(['status' => 'paid']);

        return $updated;
    }

    /**
     * Get overtime report for a period
     */
    public function getOvertimeReport($startDate, $endDate, $departmentId = null, $companyId = null)
    {
        $query = OvertimeCalculation::with(['employee.department', 'employee.company'])
                                   ->whereBetween('attendance_date', [$startDate, $endDate])
                                   ->withOvertime();

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($companyId) {
            $query->whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $calculations = $query->get();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'summary' => [
                'total_employees' => $calculations->pluck('employee_id')->unique()->count(),
                'total_overtime_amount' => $calculations->sum('overtime_amount'),
                'total_overtime_hours' => round($calculations->sum('net_overtime_minutes') / 60, 2),
                'total_overtime_days' => $calculations->count()
            ],
            'by_employee' => $calculations->groupBy('employee_id')->map(function ($employeeCalcs, $employeeId) {
                $employee = $employeeCalcs->first()->employee;
                return [
                    'employee_id' => $employeeId,
                    'employee_name' => $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name),
                    'department' => $employee->department->department_name ?? 'N/A',
                    'total_amount' => $employeeCalcs->sum('overtime_amount'),
                    'total_hours' => round($employeeCalcs->sum('net_overtime_minutes') / 60, 2),
                    'days_count' => $employeeCalcs->count()
                ];
            })->values()
        ];
    }

    /**
     * Auto-process overtime for recent attendances
     * Useful for background jobs
     */
    public function autoProcessRecentOvertime($daysBack = 7)
    {
        $startDate = Carbon::now()->subDays($daysBack)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');

        return $this->processOvertimeForDateRange($startDate, $endDate);
    }
}