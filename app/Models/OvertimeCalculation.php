<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OvertimeCalculation extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'clock_in',
        'clock_out',
        'shift_start_time',
        'shift_end_time',
        'working_minutes',
        'shift_minutes',
        'late_minutes',
        'overtime_minutes',
        'net_overtime_minutes',
        'hourly_rate',
        'overtime_rate',
        'overtime_amount',
        'overtime_eligible',
        'required_hours_per_day',
        'basic_salary',
        'calculation_notes',
        'shift_name',
        'status',
        'calculated_at'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'clock_in' => 'datetime:H:i:s',
        'clock_out' => 'datetime:H:i:s',
        'shift_start_time' => 'datetime:H:i:s',
        'shift_end_time' => 'datetime:H:i:s',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'overtime_eligible' => 'boolean',
        'calculated_at' => 'datetime'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'employee_id', 'employee_id')
                    ->whereColumn('attendance_date', 'attendance_date');
    }

    // Scopes
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('attendance_date', $year)
                    ->whereMonth('attendance_date', $month);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeWithOvertime($query)
    {
        return $query->where('net_overtime_minutes', '>', 0);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Accessors
    public function getOvertimeHoursAttribute()
    {
        return round($this->net_overtime_minutes / 60, 2);
    }

    public function getWorkingHoursAttribute()
    {
        return round($this->working_minutes / 60, 2);
    }

    public function getShiftHoursAttribute()
    {
        return round($this->shift_minutes / 60, 2);
    }

    public function getLateHoursAttribute()
    {
        return round($this->late_minutes / 60, 2);
    }

    // Methods
    public static function calculateAndStore($attendance, $employee)
    {
        // Get shift information from attendance record first, fallback to employee's current shift
        $shift = null;
        if (isset($attendance->office_shift_id) && $attendance->office_shift_id) {
            $shift = \App\Models\OfficeShift::find($attendance->office_shift_id);
        }

        // Fallback to employee's current shift if no shift in attendance record
        if (!$shift) {
            $shift = $employee->officeShift;
        }

        if (!$shift) {
            \Log::warning("No shift found for attendance calculation", [
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'attendance_shift_id' => $attendance->office_shift_id ?? 'null',
                'employee_shift_id' => $employee->office_shift_id ?? 'null'
            ]);
            return null;
        }

        $dayOfWeek = strtolower(Carbon::parse($attendance->attendance_date)->format('l'));
        $shiftStartTime = self::getShiftStartTime($shift, $dayOfWeek);
        $shiftEndTime = self::getShiftEndTime($shift, $dayOfWeek);

        if (!$shiftStartTime || !$shiftEndTime) {
            return null;
        }

        // Parse times
        $clockInTime = Carbon::parse($attendance->attendance_date . ' ' . $attendance->clock_in);
        $clockOutTime = Carbon::parse($attendance->attendance_date . ' ' . $attendance->clock_out);
        $expectedStartTime = Carbon::parse($attendance->attendance_date . ' ' . $shiftStartTime);
        $expectedEndTime = Carbon::parse($attendance->attendance_date . ' ' . $shiftEndTime);

        // Calculate working minutes
        $workingMinutes = $clockInTime->diffInMinutes($clockOutTime);
        $shiftMinutes = $expectedStartTime->diffInMinutes($expectedEndTime);

        // Calculate late minutes with 15-minute grace period
        $rawLateMinutes = $clockInTime->gt($expectedStartTime) ? $clockInTime->diffInMinutes($expectedStartTime) : 0;
        $lateMinutes = $rawLateMinutes > 15 ? $rawLateMinutes : 0; // 15-minute grace period

        // Calculate overtime
        $overtimeMinutes = $clockOutTime->gt($expectedEndTime) ? $clockOutTime->diffInMinutes($expectedEndTime) : 0;

        // Adjust for late time (only deduct actual late minutes after grace period)
        $netOvertimeMinutes = max(0, $overtimeMinutes - $lateMinutes);

        // Calculate rates
        $dailySalary = $employee->basic_salary / 26;
        $hourlyRate = $dailySalary / ($employee->required_hours_per_day ?: 9);
        $overtimeRate = $hourlyRate * 2;
        $overtimeAmount = ($netOvertimeMinutes / 60) * $overtimeRate;

        // Create or update record
        return self::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $attendance->attendance_date
            ],
            [
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
                'shift_start_time' => $shiftStartTime,
                'shift_end_time' => $shiftEndTime,
                'working_minutes' => $workingMinutes,
                'shift_minutes' => $shiftMinutes,
                'late_minutes' => $lateMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'net_overtime_minutes' => $netOvertimeMinutes,
                'hourly_rate' => $hourlyRate,
                'overtime_rate' => $overtimeRate,
                'overtime_amount' => $overtimeAmount,
                'overtime_eligible' => $employee->overtime_allowed,
                'required_hours_per_day' => $employee->required_hours_per_day ?: 9,
                'basic_salary' => $employee->basic_salary,
                'shift_name' => $shift->shift_name ?? 'Unknown',
                'status' => 'calculated',
                'calculated_at' => now()
            ]
        );
    }

    public static function getMonthlyOvertimeForEmployee($employeeId, $year, $month)
    {
        return self::forEmployee($employeeId)
                  ->forMonth($year, $month)
                  ->withOvertime()
                  ->sum('overtime_amount');
    }

    public static function getMonthlyOvertimeHoursForEmployee($employeeId, $year, $month)
    {
        return self::forEmployee($employeeId)
                  ->forMonth($year, $month)
                  ->withOvertime()
                  ->sum('net_overtime_minutes') / 60;
    }

    private static function getShiftStartTime($shift, $dayOfWeek)
    {
        $column = $dayOfWeek . '_in';
        return $shift->{$column} ?? $shift->start_time ?? null;
    }

    private static function getShiftEndTime($shift, $dayOfWeek)
    {
        $column = $dayOfWeek . '_out';
        return $shift->{$column} ?? $shift->end_time ?? null;
    }

    // Mark as verified
    public function markAsVerified()
    {
        $this->update(['status' => 'verified']);
    }

    // Mark as paid
    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }
}