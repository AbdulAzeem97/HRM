<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\office_shift;
use Carbon\Carbon;

class LaborEmployeeService
{
    /**
     * Mark selected employees as labor employees
     */
    public static function markEmployeesAsLabor($employeeIds)
    {
        if (empty($employeeIds)) {
            return ['error' => 'No employee IDs provided'];
        }

        $updated = Employee::markAsLaborEmployee($employeeIds);
        
        return [
            'success' => true,
            'updated_count' => $updated,
            'message' => "Successfully marked {$updated} employees as labor employees"
        ];
    }

    /**
     * Bulk assign labor status by department or designation
     */
    public static function markEmployeesByCategory($type, $categoryIds, $companyId = null)
    {
        $query = Employee::where('is_active', true);
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        switch ($type) {
            case 'department':
                $query->whereIn('department_id', $categoryIds);
                break;
            case 'designation':
                $query->whereIn('designation_id', $categoryIds);
                break;
            default:
                return ['error' => 'Invalid category type. Use "department" or "designation"'];
        }

        $employeeIds = $query->pluck('id')->toArray();
        
        if (empty($employeeIds)) {
            return ['error' => 'No employees found for the selected criteria'];
        }

        $updated = Employee::markAsLaborEmployee($employeeIds);
        
        return [
            'success' => true,
            'updated_count' => $updated,
            'employee_ids' => $employeeIds,
            'message' => "Successfully marked {$updated} employees as labor employees based on {$type}"
        ];
    }

    /**
     * Get all labor employees with their details
     */
    public static function getAllLaborEmployees($companyId = null)
    {
        $employees = Employee::getAllLaborEmployees($companyId);
        
        return [
            'success' => true,
            'count' => $employees->count(),
            'employees' => $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->getFullNameAttribute(),
                    'staff_id' => $employee->staff_id,
                    'email' => $employee->email,
                    'department' => $employee->department->department_name ?? 'N/A',
                    'designation' => $employee->designation->designation ?? 'N/A',
                    'current_shift' => $employee->officeShift->shift_name ?? 'No shift assigned',
                    'basic_salary' => $employee->basic_salary,
                    'joining_date' => $employee->joining_date
                ];
            })
        ];
    }

    /**
     * Remove employees from shift assignments (prepare for auto-shift detection)
     */
    public static function removeShiftAssignments($employeeIds = null)
    {
        $query = Employee::laborEmployees();
        
        if ($employeeIds) {
            $query->whereIn('id', $employeeIds);
        }

        $updated = $query->update(['office_shift_id' => null]);
        
        return [
            'success' => true,
            'updated_count' => $updated,
            'message' => "Removed shift assignments for {$updated} labor employees. They will now use auto-shift detection."
        ];
    }

    /**
     * Process attendance for all labor employees on a specific date using smart shift detection
     */
    public static function processLaborAttendanceForDate($date, $companyId = null)
    {
        // Get all labor employees
        $laborEmployees = Employee::getAllLaborEmployees($companyId);
        
        if ($laborEmployees->isEmpty()) {
            return [
                'error' => 'No labor employees found',
                'processed_count' => 0
            ];
        }

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($laborEmployees as $employee) {
            // Check if there's attendance data for this employee on this date
            // This would typically come from biometric punch data or manual entry
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->where('attendance_date', Carbon::parse($date)->format('Y-m-d'))
                ->first();

            if ($existingAttendance && $existingAttendance->in_time && $existingAttendance->out_time) {
                // Process with smart shift detection
                try {
                    $result = AttendanceProcessor::processAttendance(
                        $employee->id,
                        $existingAttendance->in_time,
                        $existingAttendance->out_time,
                        $date,
                        true // Is labor employee
                    );

                    if (isset($result['success']) && $result['success']) {
                        $successCount++;
                        $results[$employee->id] = [
                            'employee_name' => $employee->getFullNameAttribute(),
                            'status' => 'success',
                            'shift_detected' => $result['shift_detected'],
                            'working_hours' => $result['calculations']['working_hours'],
                            'attendance_status' => $result['calculations']['attendance_status']
                        ];
                    } else {
                        $errorCount++;
                        $results[$employee->id] = [
                            'employee_name' => $employee->getFullNameAttribute(),
                            'status' => 'error',
                            'error' => $result['error'] ?? 'Unknown error'
                        ];
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $results[$employee->id] = [
                        'employee_name' => $employee->getFullNameAttribute(),
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                $results[$employee->id] = [
                    'employee_name' => $employee->getFullNameAttribute(),
                    'status' => 'skipped',
                    'reason' => 'No punch data available for this date'
                ];
            }
        }

        return [
            'success' => true,
            'processed_date' => $date,
            'total_labor_employees' => $laborEmployees->count(),
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'results' => $results
        ];
    }

    /**
     * Get statistics for labor employees
     */
    public static function getLaborEmployeeStats($companyId = null)
    {
        $totalEmployees = Employee::where('is_active', true);
        $laborEmployees = Employee::laborEmployees();
        
        if ($companyId) {
            $totalEmployees->where('company_id', $companyId);
            $laborEmployees->where('company_id', $companyId);
        }

        $totalCount = $totalEmployees->count();
        $laborCount = $laborEmployees->count();
        $regularCount = $totalCount - $laborCount;

        return [
            'total_employees' => $totalCount,
            'labor_employees' => $laborCount,
            'regular_employees' => $regularCount,
            'labor_percentage' => $totalCount > 0 ? round(($laborCount / $totalCount) * 100, 2) : 0
        ];
    }
}