<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\office_shift;
use Carbon\Carbon;

class AttendanceProcessor
{
    public static function processAttendance($employeeId, $punchInTime, $punchOutTime, $attendanceDate, $isLaborEmployee = true)
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return ['error' => 'Employee not found'];
        }

        // Use smart shift detection for labor employees who work multiple shifts
        if ($isLaborEmployee) {
            // Allow employees with any working hours for shift detection
            $smartDetection = Attendance::smartShiftDetection($punchInTime, $punchOutTime, false);
            
            if (!$smartDetection['validation_passed']) {
                return [
                    'error' => $smartDetection['message'],
                    'smart_detection' => $smartDetection
                ];
            }
            
            $detectedShiftName = $smartDetection['shift_selected'];
            $shift = office_shift::where('shift_name', $detectedShiftName)->first();
            
            if (!$shift) {
                return [
                    'error' => 'Could not find shift configuration for: ' . $detectedShiftName,
                    'smart_detection' => $smartDetection
                ];
            }
        } else {
            // Use original detection for regular employees
            $detectedShiftName = Attendance::detectShift($punchInTime);
            $shift = office_shift::where('shift_name', $detectedShiftName)->first();
            
            if (!$shift) {
                return ['error' => 'Could not detect shift for punch time: ' . $punchInTime];
            }
        }

        // Get shift timings for the day of week
        $dayOfWeek = strtolower(Carbon::parse($attendanceDate)->format('l'));
        $shiftStartField = $dayOfWeek . '_in';
        $shiftEndField = $dayOfWeek . '_out';
        
        $shiftStart = Carbon::createFromTimeString($shift->$shiftStartField);
        $shiftEnd = Carbon::createFromTimeString($shift->$shiftEndField);
        
        // Calculate shift duration
        $shiftDuration = $shiftEnd->diffInHours($shiftStart);
        
        // Parse punch times
        $punchIn = Carbon::parse($punchInTime);
        $punchOut = Carbon::parse($punchOutTime);
        
        // Calculate working hours
        $workingHours = $punchOut->diffInHours($punchIn);
        
        // Calculate late minutes
        $lateMinutes = max(0, $punchIn->diffInMinutes($shiftStart));
        
        // Check for half days
        $isIncomingHalfDay = Attendance::isIncomingHalfDay($punchInTime, $detectedShiftName);
        $isOutgoingHalfDay = Attendance::isOutgoingHalfDay($punchOutTime, $detectedShiftName);
        $isHalfDay = $isIncomingHalfDay || $isOutgoingHalfDay;
        
        // Calculate deductions and overtime
        $lateDeduction = Attendance::calculateLateDeduction($lateMinutes, $employee->salary ?? 50000);
        $overtimeData = Attendance::calculateOvertime($workingHours, $shiftDuration, $lateMinutes, $employee->salary ?? 50000);
        
        // Handle early leave deduction for labor employees
        $earlyLeaveDeduction = 0;
        $earlyLeaveMinutes = 0;
        
        if ($isLaborEmployee && isset($smartDetection['early_leave'])) {
            $earlyLeaveMinutes = $smartDetection['early_leave']['early_leave_minutes'];
            $earlyLeaveDeduction = Attendance::calculateEarlyLeaveDeduction($earlyLeaveMinutes, $employee->salary ?? 50000);
        }
        
        // Determine attendance status
        if ($isLaborEmployee && isset($smartDetection['early_leave'])) {
            $attendanceStatus = $smartDetection['early_leave']['working_status'];
        } else {
            $attendanceStatus = 'Present';
            if ($isHalfDay) {
                $attendanceStatus = 'Half Day';
            }
        }
        
        // Create or update attendance record
        $attendanceData = [
            'employee_id' => $employeeId,
            'attendance_date' => Carbon::parse($attendanceDate)->format('Y-m-d'),
            'in_time' => $punchIn->format('H:i:s'),
            'out_time' => $punchOut->format('H:i:s'),
            'working_hours' => $workingHours,
            'overtime_hours' => $overtimeData['overtime_hours'],
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
            'shift_id' => $shift->id,
            'is_half_day' => ($attendanceStatus === 'Half Day') ? 1 : 0,
            'late_deduction' => $lateDeduction,
            'early_leave_deduction' => $earlyLeaveDeduction,
            'overtime_amount' => $overtimeData['overtime_amount'],
            'attendance_status' => $attendanceStatus
        ];
        
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'attendance_date' => Carbon::parse($attendanceDate)->format('Y-m-d')
            ],
            $attendanceData
        );
        
        $response = [
            'success' => true,
            'attendance' => $attendance,
            'shift_detected' => $detectedShiftName,
            'calculations' => [
                'late_minutes' => $lateMinutes,
                'late_deduction' => $lateDeduction,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'early_leave_deduction' => $earlyLeaveDeduction,
                'overtime_hours' => $overtimeData['overtime_hours'],
                'overtime_amount' => $overtimeData['overtime_amount'],
                'extra_ot_hours' => $overtimeData['extra_ot_hours'],
                'is_half_day' => ($attendanceStatus === 'Half Day'),
                'half_day_type' => $isIncomingHalfDay ? 'Incoming' : ($isOutgoingHalfDay ? 'Outgoing' : null),
                'working_hours' => $workingHours,
                'shift_duration' => $shiftDuration,
                'attendance_status' => $attendanceStatus
            ]
        ];

        // Add smart detection details for labor employees
        if ($isLaborEmployee && isset($smartDetection)) {
            $response['smart_shift_detection'] = $smartDetection;
        }

        return $response;
    }

    public static function generateDailyReport($date, $companyId = null)
    {
        $query = Attendance::with(['employee', 'shift'])
            ->where('attendance_date', Carbon::parse($date)->format('Y-m-d'));
            
        if ($companyId) {
            $query->whereHas('employee', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }
        
        $attendances = $query->get();
        
        $report = [];
        foreach ($attendances as $attendance) {
            $employee = $attendance->employee;
            $shift = $attendance->shift;
            
            $report[] = [
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'employee_id' => $employee->id,
                'shift_name' => $shift->shift_name ?? 'N/A',
                'in_time' => $attendance->in_time,
                'out_time' => $attendance->out_time,
                'working_hours' => $attendance->working_hours,
                'late_minutes' => $attendance->late_minutes,
                'late_deduction' => number_format($attendance->late_deduction, 2),
                'overtime_hours' => $attendance->overtime_hours,
                'overtime_amount' => number_format($attendance->overtime_amount, 2),
                'is_half_day' => $attendance->is_half_day ? 'Yes' : 'No',
                'attendance_status' => $attendance->attendance_status,
                'per_day_salary' => number_format(Attendance::getPerDaySalary($employee->salary ?? 50000), 2)
            ];
        }
        
        return $report;
    }

    public static function getExtraOTReport($date, $companyId = null)
    {
        $query = Attendance::with(['employee', 'shift'])
            ->where('attendance_date', Carbon::parse($date)->format('Y-m-d'))
            ->where('overtime_hours', '>', 0);
            
        if ($companyId) {
            $query->whereHas('employee', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }
        
        $attendances = $query->get();
        
        $extraOTReport = [];
        foreach ($attendances as $attendance) {
            $employee = $attendance->employee;
            $shift = $attendance->shift;
            
            // Recalculate to get extra OT hours
            $overtimeData = Attendance::calculateOvertime(
                $attendance->working_hours,
                8, // Default 8-hour shift, you may want to calculate from actual shift
                $attendance->late_minutes,
                $employee->salary ?? 50000
            );
            
            if ($overtimeData['extra_ot_hours'] > 0) {
                $extraOTReport[] = [
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'employee_id' => $employee->id,
                    'shift_name' => $shift->shift_name ?? 'N/A',
                    'total_ot_hours' => $overtimeData['overtime_hours'] + $overtimeData['extra_ot_hours'],
                    'payroll_ot_hours' => $overtimeData['overtime_hours'],
                    'extra_ot_hours' => $overtimeData['extra_ot_hours'],
                    'extra_ot_rate' => number_format(($employee->salary ?? 50000) / (26 * 8) * 2, 2),
                    'extra_ot_amount' => number_format($overtimeData['extra_ot_hours'] * (($employee->salary ?? 50000) / (26 * 8) * 2), 2)
                ];
            }
        }
        
        return $extraOTReport;
    }
}