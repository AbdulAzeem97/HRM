<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeShiftChange;
use App\Models\Attendance;
use App\Models\OfficeShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeShiftManagementService
{
    /**
     * Change an employee's shift starting from a specific date
     */
    public function changeEmployeeShift($employeeId, $newShiftId, $effectiveDate, $changedBy = null, $reason = null)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($employeeId);
            $newShift = OfficeShift::findOrFail($newShiftId);
            $oldShiftId = $employee->office_shift_id;

            // Record the shift change in history
            $shiftChange = EmployeeShiftChange::create([
                'employee_id' => $employeeId,
                'old_shift_id' => $oldShiftId,
                'new_shift_id' => $newShiftId,
                'effective_date' => $effectiveDate,
                'changed_by' => $changedBy,
                'reason' => $reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update employee's current shift
            $employee->update(['office_shift_id' => $newShiftId]);

            // Update any future attendance records (from effective date onwards) with new shift
            $this->updateFutureAttendanceShifts($employeeId, $newShiftId, $effectiveDate);

            DB::commit();

            Log::info("Employee shift changed successfully", [
                'employee_id' => $employeeId,
                'old_shift_id' => $oldShiftId,
                'new_shift_id' => $newShiftId,
                'effective_date' => $effectiveDate,
                'shift_change_id' => $shiftChange->id
            ]);

            return [
                'success' => true,
                'shift_change' => $shiftChange,
                'message' => "Shift changed from {$employee->officeShift->shift_name} to {$newShift->shift_name} effective {$effectiveDate}"
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Failed to change employee shift", [
                'employee_id' => $employeeId,
                'new_shift_id' => $newShiftId,
                'effective_date' => $effectiveDate,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to change shift: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get the correct shift for an employee on a specific date
     */
    public function getEmployeeShiftForDate($employeeId, $date)
    {
        // Look for the most recent shift change before or on the given date
        $shiftChange = EmployeeShiftChange::where('employee_id', $employeeId)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();

        if ($shiftChange) {
            return OfficeShift::find($shiftChange->new_shift_id);
        }

        // Fallback to employee's current shift
        $employee = Employee::find($employeeId);
        return $employee ? $employee->officeShift : null;
    }

    /**
     * Update attendance records with correct shift information
     */
    public function updateAttendanceShiftData($employeeId, $startDate = null, $endDate = null)
    {
        $query = Attendance::where('employee_id', $employeeId);

        if ($startDate) {
            $query->where('attendance_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('attendance_date', '<=', $endDate);
        }

        $attendances = $query->get();
        $updated = 0;

        foreach ($attendances as $attendance) {
            $correctShift = $this->getEmployeeShiftForDate($employeeId, $attendance->attendance_date);

            if ($correctShift && $attendance->office_shift_id != $correctShift->id) {
                $attendance->update(['office_shift_id' => $correctShift->id]);
                $updated++;

                // Recalculate overtime for this attendance
                $employee = Employee::find($employeeId);
                if ($employee && $employee->overtime_allowed) {
                    $overtimeService = app(OvertimeCalculationService::class);
                    $overtimeService->processAttendanceOvertime($attendance->fresh());
                }
            }
        }

        return $updated;
    }

    /**
     * Get employee's shift history
     */
    public function getEmployeeShiftHistory($employeeId)
    {
        return EmployeeShiftChange::where('employee_id', $employeeId)
            ->with(['oldShift', 'newShift', 'changedByUser'])
            ->orderBy('effective_date', 'desc')
            ->get();
    }

    /**
     * Get employees with multiple shifts in a given month
     */
    public function getEmployeesWithMultipleShiftsInMonth($year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return DB::select("
            SELECT
                e.id as employee_id,
                CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                COUNT(DISTINCT a.office_shift_id) as shift_count,
                GROUP_CONCAT(DISTINCT os.shift_name) as shifts_used
            FROM employees e
            JOIN attendances a ON e.id = a.employee_id
            JOIN office_shifts os ON a.office_shift_id = os.id
            WHERE a.attendance_date BETWEEN ? AND ?
            GROUP BY e.id, e.first_name, e.last_name
            HAVING shift_count > 1
            ORDER BY shift_count DESC, employee_name
        ", [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
    }

    /**
     * Update future attendance records with new shift
     */
    private function updateFutureAttendanceShifts($employeeId, $newShiftId, $effectiveDate)
    {
        return Attendance::where('employee_id', $employeeId)
            ->where('attendance_date', '>=', $effectiveDate)
            ->update(['office_shift_id' => $newShiftId]);
    }

    /**
     * Ensure attendance record has correct shift before processing
     */
    public static function ensureAttendanceShift(Attendance $attendance)
    {
        if (!$attendance->office_shift_id) {
            $service = new self();
            $correctShift = $service->getEmployeeShiftForDate(
                $attendance->employee_id,
                $attendance->attendance_date
            );

            if ($correctShift) {
                $attendance->update(['office_shift_id' => $correctShift->id]);
                return $correctShift;
            }
        }

        return $attendance->office_shift_id ? OfficeShift::find($attendance->office_shift_id) : null;
    }
}