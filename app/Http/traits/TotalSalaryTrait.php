<?php

namespace App\Http\traits;

use Carbon\Carbon;

Trait TotalSalaryTrait {

	/**
	 * Helper method to safely sum numeric values that might contain comma formatting
	 * Removes commas and converts to float before summing
	 */
	private function safeSum($collection, $field)
	{
		return $collection->sum(function($item) use ($field) {
			$value = $item->{$field} ?? 0;
			// Remove commas and convert to float
			$cleanValue = str_replace(',', '', $value);
			return (float)$cleanValue;
		});
	}

	/**
	 * Calculate total salary with corrected business rules:
	 * 1. Late = clocks in more than 15 minutes after shift start
	 * 2. Half-Day = clocks in more than 2 hours late (deduct 50% daily salary)
	 * 3. No late accumulation rule (removed)
	 * 4. Overtime starts only after shift end time (e.g., 5:15 PM)
	 * 5. Overtime rate = (daily salary ÷ required_hours) × 2 (double pay)
	 * 6. If late and overtime, subtract late time from overtime first
	 * 7. Overtime only if employee.overtime_allowed = true
	 * 8. Daily salary calculated as basic_salary ÷ 26 working days
	 */
	public function totalSalary($employee, $payslip_type, $basic_salary, $allowance_amount, $deduction_amount, $pension_amount, $total_minutes = 1, $month_year = null){

        $total_commission_amount = $this->safeSum($employee->commissions, 'commission_amount');
	    $total_monthly_payable = $this->safeSum($employee->loans, 'monthly_payable');
        $total_other_payment_amount = $this->safeSum($employee->otherPayments, 'other_payment_amount');
        
        // Calculate overtime and deductions based on new business rules
        $overtime_pay = 0;
        $late_deductions = 0;

        if ($month_year) {
            \Log::info('TotalSalary: Calculating attendance for employee ' . $employee->id, [
                'month_year' => $month_year,
                'basic_salary' => $basic_salary
            ]);

            $attendanceData = $this->calculateAttendanceDeductionsAndOvertime($employee, $basic_salary, $month_year);
            $overtime_pay = $attendanceData['overtime_pay'];
            $late_deductions = $attendanceData['late_deductions'];

            \Log::info('TotalSalary: Attendance calculation results', [
                'overtime_pay' => $overtime_pay,
                'late_deductions' => $late_deductions,
                'late_days_count' => $attendanceData['late_days_count'] ?? 0,
                'half_days_count' => $attendanceData['half_days_count'] ?? 0,
                'overtime_hours' => $attendanceData['overtime_hours'] ?? 0
            ]);
        }

		if($payslip_type == 'Monthly')
		{
			$total = $basic_salary + $allowance_amount + $total_commission_amount
				- $total_monthly_payable - $deduction_amount - $pension_amount
				+ $total_other_payment_amount + $overtime_pay - $late_deductions;

			\Log::info('TotalSalary: Final calculation breakdown', [
				'basic_salary' => $basic_salary,
				'allowance_amount' => $allowance_amount,
				'total_commission_amount' => $total_commission_amount,
				'total_monthly_payable' => $total_monthly_payable,
				'deduction_amount' => $deduction_amount,
				'pension_amount' => $pension_amount,
				'total_other_payment_amount' => $total_other_payment_amount,
				'overtime_pay' => $overtime_pay,
				'late_deductions' => $late_deductions,
				'final_total' => $total
			]);
		}
		else
		{
			// For hourly employees, calculate based on worked time but still apply attendance rules
			$worked_amount = ($basic_salary / 60) * $total_minutes;
			$total = $worked_amount + $allowance_amount + $total_commission_amount
				- $total_monthly_payable - $deduction_amount - $pension_amount
				+ $total_other_payment_amount + $overtime_pay - $late_deductions;
		}

        if($total < 0)
        {
            $total = 0;
        }
		return $total;
	}

	/**
	 * Calculate attendance-based deductions and overtime pay according to corrected business rules
	 */
	private function calculateAttendanceDeductionsAndOvertime($employee, $basic_salary, $month_year)
	{
		// Validate inputs
		if (!is_numeric($basic_salary) || $basic_salary <= 0) {
			throw new \Exception('Invalid basic salary: ' . $basic_salary);
		}

		if (empty($month_year)) {
			throw new \Exception('Month year is empty');
		}

		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date = date('Y-m-d', strtotime('last day of ' . $month_year));

		if (!$first_date || !$last_date) {
			throw new \Exception('Invalid month year format: ' . $month_year);
		}

		// Get employee's shift and required hours per day
		$required_hours_per_day = (float)($employee->required_hours_per_day ?? 9);
		if ($required_hours_per_day <= 0) {
			$required_hours_per_day = 9;
		}

		$shift = $employee->officeShift;

		\Log::info('Shift data debug', [
			'employee_id' => $employee->id,
			'office_shift_id' => $employee->office_shift_id ?? 'NULL',
			'shift_loaded' => $shift ? 'YES' : 'NO',
			'shift_data' => $shift ? [
				'id' => $shift->id,
				'shift_name' => $shift->shift_name,
				'monday_in' => $shift->monday_in,
				'monday_out' => $shift->monday_out,
				'friday_in' => $shift->friday_in,
				'friday_out' => $shift->friday_out
			] : 'NO_SHIFT_DATA'
		]);

		// Get all attendance records for the month
		$attendances = $employee->employeeAttendance()
			->whereBetween('attendance_date', [$first_date, $last_date])
			->get();

		\Log::info('Attendance records loaded', [
			'employee_id' => $employee->id,
			'first_date' => $first_date,
			'last_date' => $last_date,
			'attendance_count' => $attendances->count(),
			'attendances' => $attendances->map(function($att) {
				return [
					'date' => $att->attendance_date,
					'clock_in' => $att->clock_in,
					'clock_out' => $att->clock_out
				];
			})->toArray()
		]);

		$late_days_count = 0;
		$half_days_count = 0;
		$total_overtime_minutes = 0;
		$daily_salary = (float)$basic_salary / 26; // Corrected to 26 working days per month

		foreach ($attendances as $attendance) {
			$dayOfWeek = strtolower(Carbon::parse($attendance->attendance_date)->format('l'));
			$shiftStartTime = $this->getShiftStartTime($shift, $dayOfWeek);
			$shiftEndTime = $this->getShiftEndTime($shift, $dayOfWeek);

			if (!$shiftStartTime) {
				\Log::info('SKIPPING DAY - No shift start time', [
					'date' => $attendance->attendance_date,
					'day_of_week' => $dayOfWeek,
					'shift_start' => $shiftStartTime,
					'shift_loaded' => $shift ? 'YES' : 'NO'
				]);
				continue; // Skip if no shift defined for this day
			}

			\Log::info('Processing attendance day', [
				'date' => $attendance->attendance_date,
				'day_of_week' => $dayOfWeek,
				'clock_in' => $attendance->clock_in,
				'clock_out' => $attendance->clock_out,
				'shift_start' => $shiftStartTime,
				'shift_end' => $shiftEndTime
			]);

			// Parse clock in time
			$clockInTime = Carbon::parse($attendance->attendance_date . ' ' . $attendance->clock_in);
			$expectedStartTime = Carbon::parse($attendance->attendance_date . ' ' . $shiftStartTime);
			
			// Calculate late minutes with 15-minute grace period (same as OvertimeCalculation model)
			$rawLateMinutes = $clockInTime->gt($expectedStartTime) ? $clockInTime->diffInMinutes($expectedStartTime) : 0;
			$lateMinutes = $rawLateMinutes > 15 ? $rawLateMinutes : 0; // 15-minute grace period

			// Debug logging removed for production

			// Business Rule 1: Late = more than 15 minutes after shift start
			if ($lateMinutes > 0) {
				// Business Rule 2: Half-Day = more than 2 hours (120 minutes) late
				if ($lateMinutes > 120) {
					$half_days_count++;
				} else {
					$late_days_count++;
				}
			}

			// Calculate overtime only if employee.overtime_allowed = true
			if ($employee->overtime_allowed && $attendance->clock_out) {
				$clockOutTime = Carbon::parse($attendance->attendance_date . ' ' . $attendance->clock_out);

				if ($shiftEndTime) {
					$expectedEndTime = Carbon::parse($attendance->attendance_date . ' ' . $shiftEndTime);

					// Overtime starts after shift end time (positive when working late)
					$overtimeMinutes = $clockOutTime->gt($expectedEndTime) ? $clockOutTime->diffInMinutes($expectedEndTime) : 0;

					// If late and overtime, subtract late time from overtime first
					$originalOvertimeMinutes = $overtimeMinutes;
					if ($lateMinutes > 0 && $overtimeMinutes > 0) {
						$overtimeMinutes = max(0, $overtimeMinutes - $lateMinutes);
					}

					// Debug logging removed for production

					$total_overtime_minutes += $overtimeMinutes;
				}
			}
		}

		// Calculate deductions
		$late_deductions = 0;

		// Half-day deductions (50% of daily salary)
		$late_deductions += $half_days_count * ($daily_salary * 0.5);

		// Calculate overtime pay
		// Business Rule 4: Overtime rate = (daily salary ÷ required_hours) × 2 (double pay)
		$hourly_rate = (float)$daily_salary / (float)$required_hours_per_day;
		$overtime_hourly_rate = (float)$hourly_rate * 2;
		$overtime_pay = ((float)$total_overtime_minutes / 60) * (float)$overtime_hourly_rate;

		return [
			'late_deductions' => round($late_deductions, 2),
			'overtime_pay' => round($overtime_pay, 2),
			'late_days_count' => $late_days_count,
			'half_days_count' => $half_days_count,
			'overtime_hours' => round((float)$total_overtime_minutes / 60, 2)
		];
	}

	/**
	 * Get shift start time for a specific day of the week
	 */
	private function getShiftStartTime($shift, $dayOfWeek)
	{
		if (!$shift) return null;

		$time = null;
		switch ($dayOfWeek) {
			case 'monday': $time = $shift->monday_in; break;
			case 'tuesday': $time = $shift->tuesday_in; break;
			case 'wednesday': $time = $shift->wednesday_in; break;
			case 'thursday': $time = $shift->thursday_in; break;
			case 'friday': $time = $shift->friday_in; break;
			case 'saturday': $time = $shift->saturday_in; break;
			case 'sunday': $time = $shift->sunday_in; break;
			default: return null;
		}

		return $this->convertTo24HourFormat($time);
	}

	/**
	 * Get shift end time for a specific day of the week
	 */
	private function getShiftEndTime($shift, $dayOfWeek)
	{
		if (!$shift) return null;

		$time = null;
		switch ($dayOfWeek) {
			case 'monday': $time = $shift->monday_out; break;
			case 'tuesday': $time = $shift->tuesday_out; break;
			case 'wednesday': $time = $shift->wednesday_out; break;
			case 'thursday': $time = $shift->thursday_out; break;
			case 'friday': $time = $shift->friday_out; break;
			case 'saturday': $time = $shift->saturday_out; break;
			case 'sunday': $time = $shift->sunday_out; break;
			default: return null;
		}

		return $this->convertTo24HourFormat($time);
	}

	/**
	 * Convert 12-hour AM/PM format to 24-hour format
	 */
	private function convertTo24HourFormat($time)
	{
		if (empty($time)) return null;

		// If already in 24-hour format (HH:MM), return as is
		if (preg_match('/^\d{2}:\d{2}$/', $time)) {
			return $time;
		}

		// Convert 12-hour format with AM/PM to 24-hour format
		try {
			return Carbon::createFromFormat('h:iA', $time)->format('H:i');
		} catch (\Exception $e) {
			// Fallback for different formats
			try {
				return Carbon::createFromFormat('g:iA', $time)->format('H:i');
			} catch (\Exception $e2) {
				return null;
			}
		}
	}
}
