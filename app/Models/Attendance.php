<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

	protected $guarded = [];

	public $timestamps = false;

	protected $fillable = [
		'employee_id', 'attendance_date', 'clock_in', 'clock_out', 
		'clock_in_ip', 'clock_out_ip', 'clock_in_out', 'time_late', 
		'early_leaving', 'overtime', 'total_work', 'total_rest', 'attendance_status'
	];

	public function employee(){
		return $this->belongsTo('App\Models\Employee', 'employee_id');
	}

	public function shift(){
		return $this->belongsTo('App\Models\office_shift', 'shift_id');
	}

	public function setAttendanceDateAttribute($value)
	{
		// Convert date to Y-m-d format for database storage
		if ($value) {
			try {
				$this->attributes['attendance_date'] = Carbon::parse($value)->format('Y-m-d');
			} catch (\Exception $e) {
				$this->attributes['attendance_date'] = null;
			}
		} else {
			$this->attributes['attendance_date'] = null;
		}
	}

	public function getAttendanceDateAttribute($value)
	{
		// Return date in the format specified in .env for display
		if ($value) {
			try {
				return Carbon::parse($value)->format(env('Date_Format', 'd-m-Y'));
			} catch (\Exception $e) {
				return $value;
			}
		}
		return $value;
	}

	public static function detectShift($punchInTime)
	{
		$shifts = [
			'Shift-A' => ['start' => '07:00', 'end' => '15:45', 'tolerance' => 120], // 2 hours tolerance
			'General' => ['start' => '08:00', 'end' => '17:15', 'tolerance' => 120],
			'11:00-20:15' => ['start' => '11:00', 'end' => '20:15', 'tolerance' => 120],
			'Shift-B' => ['start' => '15:00', 'end' => '23:45', 'tolerance' => 120],
			'19:00-04:15' => ['start' => '19:00', 'end' => '04:15', 'tolerance' => 120],
			'Shift-C' => ['start' => '23:00', 'end' => '07:15', 'tolerance' => 120]
		];

		$punchTime = Carbon::parse($punchInTime);
		$bestMatch = null;
		$minDifference = PHP_INT_MAX;

		foreach ($shifts as $shiftName => $shift) {
			$shiftStart = Carbon::createFromTimeString($shift['start']);
			$difference = abs($punchTime->diffInMinutes($shiftStart));
			
			if ($difference <= $shift['tolerance'] && $difference < $minDifference) {
				$minDifference = $difference;
				$bestMatch = $shiftName;
			}
		}

		return $bestMatch;
	}

	public static function smartShiftDetection($punchInTime, $punchOutTime, $requireMinimumHours = false)
	{
		$shifts = [
			'Shift-A' => ['start' => '07:00', 'end' => '15:45', 'duration' => 8.75],
			'General' => ['start' => '08:00', 'end' => '17:15', 'duration' => 9.25],
			'11:00-20:15' => ['start' => '11:00', 'end' => '20:15', 'duration' => 9.25],
			'Shift-B' => ['start' => '15:00', 'end' => '23:45', 'duration' => 8.75],
			'19:00-04:15' => ['start' => '19:00', 'end' => '04:15', 'duration' => 9.25],
			'Shift-C' => ['start' => '23:00', 'end' => '07:15', 'duration' => 8.25]
		];

		$punchIn = Carbon::parse($punchInTime);
		$punchOut = Carbon::parse($punchOutTime);
		$totalWorkingHours = $punchOut->diffInHours($punchIn, true);

		// Only enforce minimum hours if specifically required
		if ($requireMinimumHours && $totalWorkingHours < 9) {
			return [
				'shift_selected' => null,
				'reason' => 'Less than 9 hours worked',
				'total_hours' => $totalWorkingHours,
				'message' => 'Employee worked only ' . round($totalWorkingHours, 2) . ' hours. Minimum 9 hours required for shift validation.',
				'validation_passed' => false
			];
		}

		$bestShift = null;
		$bestScore = -1;
		$bestMatch = [];

		foreach ($shifts as $shiftName => $shift) {
			$shiftStart = Carbon::createFromTimeString($shift['start']);
			$shiftEnd = Carbon::createFromTimeString($shift['end']);
			
			// Handle overnight shifts
			if ($shiftEnd->lt($shiftStart)) {
				$shiftEnd->addDay();
			}

			// Calculate how well the punch times align with this shift
			$score = 0;
			
			// Check punch-in alignment (within 3 hours tolerance)
			$punchInDiff = abs($punchIn->diffInMinutes($shiftStart));
			if ($punchInDiff <= 180) { // 3 hours tolerance
				$score += (180 - $punchInDiff) / 180 * 50; // Max 50 points for punch-in alignment
			}
			
			// Check if working time covers the shift duration (9+ hours preferred)
			$shiftCoverage = min($totalWorkingHours / 9, 1) * 30; // Max 30 points for coverage
			$score += $shiftCoverage;
			
			// Bonus for shifts that match working hours better
			$expectedEndTime = $punchIn->copy()->addHours($shift['duration']);
			$endTimeDiff = abs($punchOut->diffInMinutes($expectedEndTime));
			if ($endTimeDiff <= 240) { // 4 hours tolerance for end time
				$score += (240 - $endTimeDiff) / 240 * 20; // Max 20 points
			}

			if ($score > $bestScore) {
				$bestScore = $score;
				$bestShift = $shiftName;
				$bestMatch = [
					'shift_name' => $shiftName,
					'shift_start' => $shift['start'],
					'shift_end' => $shift['end'],
					'shift_duration' => $shift['duration'],
					'score' => $score,
					'punch_in_diff_minutes' => $punchInDiff,
					'end_time_diff_minutes' => $endTimeDiff,
					'coverage_score' => $shiftCoverage
				];
			}
		}

		// Calculate overtime (any time beyond the shift duration)
		$regularHours = isset($bestMatch['shift_duration']) ? $bestMatch['shift_duration'] : 8;
		$overtimeHours = max(0, $totalWorkingHours - $regularHours);

		// Determine early leave status
		$isEarlyLeave = false;
		$earlyLeaveMinutes = 0;
		$workingStatus = 'Full Day';
		
		if ($bestShift && isset($bestMatch['shift_duration'])) {
			$expectedHours = $bestMatch['shift_duration'];
			if ($totalWorkingHours < $expectedHours) {
				$shortageHours = $expectedHours - $totalWorkingHours;
				$earlyLeaveMinutes = $shortageHours * 60;
				$isEarlyLeave = true;
				
				// Determine if it's half day or early leave
				if ($shortageHours >= 4) {
					$workingStatus = 'Half Day';
				} else {
					$workingStatus = 'Early Leave';
				}
			}
		}

		return [
			'shift_selected' => $bestShift,
			'total_working_hours' => round($totalWorkingHours, 2),
			'regular_hours' => round($regularHours, 2),
			'overtime_hours' => round($overtimeHours, 2),
			'shift_details' => $bestMatch,
			'early_leave' => [
				'is_early_leave' => $isEarlyLeave,
				'early_leave_minutes' => round($earlyLeaveMinutes, 0),
				'working_status' => $workingStatus,
				'shortage_hours' => isset($shortageHours) ? round($shortageHours, 2) : 0
			],
			'message' => $bestShift ? 
				"Auto-selected {$bestShift}. Worked " . round($totalWorkingHours, 2) . "h (Status: {$workingStatus})" :
				'No suitable shift found for the working hours.',
			'validation_passed' => $bestShift !== null
		];
	}

	public static function calculateLateDeduction($lateMinutes, $monthlySalary)
	{
		if ($lateMinutes <= 15) {
			return 0; // 15-minute grace period
		}

		// Formula: (Monthly Salary / 26 / 8 / 60) * Late Minutes
		$perMinuteRate = $monthlySalary / (26 * 8 * 60);
		return $perMinuteRate * ($lateMinutes - 15); // Subtract grace period
	}

	public static function isIncomingHalfDay($punchInTime, $shiftName)
	{
		$halfDayCutoffs = [
			'Shift-A' => '09:00',
			'General' => '10:00',
			'11:00-20:15' => '13:00',
			'Shift-B' => '17:00',
			'19:00-04:15' => '21:00',
			'Shift-C' => '01:00'
		];

		if (!isset($halfDayCutoffs[$shiftName])) {
			return false;
		}

		$cutoffTime = Carbon::createFromTimeString($halfDayCutoffs[$shiftName]);
		$punchTime = Carbon::parse($punchInTime);

		return $punchTime->gt($cutoffTime);
	}

	public static function isOutgoingHalfDay($punchOutTime, $shiftName)
	{
		$outgoingCutoffs = [
			'Shift-A' => '11:00',
			'General' => '12:00',
			'11:00-20:15' => '15:00',
			'Shift-B' => '19:00',
			'19:00-04:15' => '23:00',
			'Shift-C' => '03:00'
		];

		if (!isset($outgoingCutoffs[$shiftName])) {
			return false;
		}

		$cutoffTime = Carbon::createFromTimeString($outgoingCutoffs[$shiftName]);
		$punchTime = Carbon::parse($punchOutTime);

		return $punchTime->lt($cutoffTime);
	}

	public static function calculateOvertime($workingHours, $shiftHours, $lateMinutes, $monthlySalary)
	{
		$overtimeMinutes = max(0, ($workingHours * 60) - ($shiftHours * 60));
		
		// Only count OT after 15 minutes of duty off time
		if ($overtimeMinutes <= 15) {
			return [
				'overtime_hours' => 0,
				'overtime_amount' => 0,
				'extra_ot_hours' => 0
			];
		}

		// Reduce late minutes from OT
		$netOvertimeMinutes = max(0, $overtimeMinutes - $lateMinutes);
		$overtimeHours = $netOvertimeMinutes / 60;

		// OT Rate: (Monthly Salary / 26 / 8) * 2
		$hourlyRate = $monthlySalary / (26 * 8);
		$overtimeRate = $hourlyRate * 2;

		// Max 2 hours per day for salary
		$payrollOT = min($overtimeHours, 2);
		$extraOT = max(0, $overtimeHours - 2);

		return [
			'overtime_hours' => $payrollOT,
			'overtime_amount' => $payrollOT * $overtimeRate,
			'extra_ot_hours' => $extraOT
		];
	}

	public static function getPerDaySalary($monthlySalary)
	{
		return $monthlySalary / 26;
	}

	public static function calculateEarlyLeaveDeduction($earlyLeaveMinutes, $monthlySalary)
	{
		if ($earlyLeaveMinutes <= 0) {
			return 0;
		}

		// Formula: (Monthly Salary / 26 / 8 / 60) * Early Leave Minutes
		$perMinuteRate = $monthlySalary / (26 * 8 * 60);
		return $perMinuteRate * $earlyLeaveMinutes;
	}

	public static function determineAttendanceStatus($totalWorkingHours, $shiftDuration, $isHalfDay = false)
	{
		if ($isHalfDay) {
			return 'Half Day';
		}
		
		if ($totalWorkingHours < $shiftDuration) {
			$shortageHours = $shiftDuration - $totalWorkingHours;
			if ($shortageHours >= 4) {
				return 'Half Day';
			} else if ($shortageHours >= 1) {
				return 'Early Leave';
			}
		}
		
		return 'Present';
	}
}
