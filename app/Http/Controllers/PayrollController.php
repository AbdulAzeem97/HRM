<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\Employee;
use App\Models\FinanceBankCash;
use App\Models\FinanceExpense;
use App\Models\FinanceTransaction;
use App\Http\traits\TotalSalaryTrait;
use App\Models\Payslip;
use App\Models\SalaryLoan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use App\Http\traits\MonthlyWorkedHours;
use App\Models\SalaryBasic;

class PayrollController extends Controller {

	use TotalSalaryTrait;
	use MonthlyWorkedHours;

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

	public function index(Request $request)
	{
		$logged_user = auth()->user();
		$companies = company::all();

		// Handle date format conversion for month filtering (same as PayslipController)
		if (empty($request->filter_month_year)) {
			$selected_date = now()->format('F-Y');
		} else {
			$month_year_input = $request->filter_month_year;

			// Check if it's in DD-MM-YYYY format and convert to F-Y format
			if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $month_year_input)) {
				// Convert DD-MM-YYYY to F-Y format
				$date_parts = explode('-', $month_year_input);
				$day = $date_parts[0];
				$month = $date_parts[1];
				$year = $date_parts[2];
				$selected_date = date('F-Y', mktime(0, 0, 0, $month, 1, $year));
			} elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $month_year_input)) {
				// Convert YYYY-MM-DD to F-Y format
				$date_parts = explode('-', $month_year_input);
				$year = $date_parts[0];
				$month = $date_parts[1];
				$day = $date_parts[2];
				$selected_date = date('F-Y', mktime(0, 0, 0, $month, 1, $year));
			} else {
				// Try to parse other date formats or assume it's already correct
				try {
					// Attempt to parse various date formats and convert to F-Y
					$timestamp = strtotime($month_year_input);
					if ($timestamp !== false) {
						$selected_date = date('F-Y', $timestamp);
					} else {
						// If parsing fails, use the input as-is (assume it's already in F-Y format)
						$selected_date = $month_year_input;
					}
				} catch (\Exception $e) {
					// Fallback to input as-is
					$selected_date = $month_year_input;
				}
			}
		}

		\Log::info('Payroll List: Date conversion', [
			'original_input' => $request->filter_month_year ?? 'empty',
			'converted_date' => $selected_date
		]);
		$first_date = date('Y-m-d', strtotime('first day of ' . $selected_date));
		$last_date = date('Y-m-d', strtotime('last day of ' . $selected_date));

		if ($logged_user->can('view-paylist'))
		{
			if (request()->ajax())
			{
				$paid_employees = Payslip::where('month_year',$selected_date)->pluck('employee_id');
				$salary_basic_employees = SalaryBasic::where('first_date','<=',$first_date)->distinct()->pluck('employee_id');

				\Log::info('Payroll List: Employee filtering debug', [
					'selected_date' => $selected_date,
					'first_date' => $first_date,
					'paid_employees_count' => $paid_employees->count(),
					'paid_employees_sample' => $paid_employees->take(5)->toArray(),
					'salary_basic_employees_count' => $salary_basic_employees->count()
				]);

				if (!empty($request->filter_company && $request->filter_department))
				{
					$employees = Employee::with(['salaryBasic' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
            			{
            				$query->where('first_date', $first_date);
            			},
						'loans'=> function ($query) use ($first_date)
						{
							$query->where('first_date','<=', $first_date)
							->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'deductions'=> function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						},
						'overtimes'=> function ($query) use ($selected_date)
						{
							$query->where('month_year', $selected_date);
						},
						'payslips' => function ($query) use ($selected_date)
						{
							$query->where('month_year', $selected_date);
						},
						'employeeAttendance' => function ($query) use ($first_date, $last_date){
							$query->whereBetween('attendance_date', [$first_date, $last_date]);
						},
						'officeShift'])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','overtime_allowed','required_hours_per_day','office_shift_id')
						->where('company_id', $request->filter_company)
						->where('department_id', $request->filter_department)
						->whereIntegerInRaw('id',$salary_basic_employees)
                        ->where('is_active',1)
                        ->where('exit_date',NULL)
						->get();

				} elseif (!empty($request->filter_company))
				{
					$employees = Employee::with(['salaryBasic' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
            			{
            				$query->where('first_date', $first_date);
            			},
						'loans'=> function ($query) use ($first_date)
						{
							$query->where('first_date','<=', $first_date)
							->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'deductions'=> function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						},
						'overtimes'=> function ($query) use ($selected_date)
						{
							$query->where('month_year', $selected_date);
						},
						'payslips' => function ($query) use ($selected_date)
						{
							$query->where('month_year', $selected_date);
						},
						'employeeAttendance' => function ($query) use ($first_date, $last_date){
							$query->whereBetween('attendance_date', [$first_date, $last_date]);
						},
						'officeShift'])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','overtime_allowed','required_hours_per_day','office_shift_id')
						->where('company_id', $request->filter_company)
						->whereIntegerInRaw('id',$salary_basic_employees)
                        ->where('is_active',1)->where('exit_date',NULL)
						->get();
				} else
				{
					$employees = Employee::with(['salaryBasic' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
						'loans'=> function ($query) use ($first_date)
						{
							$query->where('first_date','<=', $first_date)
							->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'deductions'=> function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						},
						'overtimes'=> function ($query) use ($selected_date)
						{
							$query->where('month_year', $selected_date);
						},
						'payslips' => function ($query) use ($selected_date)
						{
							$query->where('month_year', $selected_date);
						},
						'employeeAttendance' => function ($query) use ($first_date, $last_date){
							$query->whereBetween('attendance_date', [$first_date, $last_date]);
						},
						'officeShift'])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','overtime_allowed','required_hours_per_day','office_shift_id')
                        ->whereIntegerInRaw('id',$salary_basic_employees)
						->where('is_active',1)
                        ->where('exit_date',NULL)
						->get();
				}

				return datatables()->of($employees)
					->setRowId(function ($pay_list)
					{
						return $pay_list->id;
					})
					->addColumn('employee_name', function ($row)
					{
						return $row->full_name;
					})
					->addColumn('payslip_type', function ($row) use ($first_date)
					{
                        foreach ($row->salaryBasic as $salaryBasic) {
                            if($salaryBasic->first_date <= $first_date)
                            {
                                $payslip_type = $salaryBasic->payslip_type; //payslip_type
                            }
                        }
						return $payslip_type;
					})
					->addColumn('basic_salary', function ($row) use ($first_date)
					{
                        foreach ($row->salaryBasic as $salaryBasic) {
                            if($salaryBasic->first_date <= $first_date)
                            {
                                $basicsalary = $salaryBasic->basic_salary; //basic salary
                            }
                        }
						return $basicsalary;
					})
					->addColumn('net_salary', function ($row)  use ($first_date, $selected_date)
					{
						//payslip_type & basic_salary
						foreach ($row->salaryBasic as $salaryBasic) {
                            if($salaryBasic->first_date <= $first_date){
                                $payslip_type = $salaryBasic->payslip_type;
								$basicsalary = $salaryBasic->basic_salary;
                            }
                        }

                        //Pension Amount
                        if ($row->pension_type=="percentage") {
                            $pension_amount =  ($basicsalary * $row->pension_amount) /100;
                        } else {
                            $pension_amount = $row->pension_amount;
                        }

                        $type              = "getAmount";
						$allowance_amount  = $this->allowances($row, $first_date, $type);
						$deduction_amount  = $this->deductions($row, $first_date, $type);

						//Net Salary
						if ($payslip_type == 'Monthly'){
							$total_salary = $this->totalSalary($row, $payslip_type, $basicsalary, $allowance_amount, $deduction_amount, $pension_amount, 1, $selected_date);
						}
						else{
							$total = 0;
							$total_hours = $this->totalWorkedHours($row);
							sscanf($total_hours, '%d:%d', $hour, $min);
                            //converting in minute
                            $total += $hour * 30 + $min;


                            //********** Test *********/
                            // $total_overtime = 0;
							// $total_overtimes = $this->totalOvertimeHours($row);
                            // sscanf($total_overtimes, '%d:%d', $overtimeHour, $overtimeMin);
							// $total_overtime += $overtimeHour * 60 + $overtimeMin;

                            // return $total_overtime;

                            //********** Test End*********/

                            $total_salary = $this->totalSalary($row, $payslip_type, $basicsalary, $allowance_amount, $deduction_amount, $pension_amount, $total, $selected_date);


						}

						return $total_salary;

					})
					->addColumn('status', function ($row)
					{
						// Check if employee has been paid for this month
						if (!$row->payslips->isEmpty()) {
							foreach ($row->payslips as $payslip) {
								if ($payslip->status == 1) {
									return 1; // Return 1 for paid status
								}
							}
						}
						// If no payslip exists or status is not 1, return 0 for unpaid
						return 0;
					})
					->addColumn('action', function ($data)
					{
						if (auth()->user()->can('view-paylist'))
						{
							$button = '<button type="button" name="view" id="' . $data->id . '" class="details btn btn-primary btn-sm" title="Details"><i class="dripicons-preview"></i></button>';
							$button .= '&nbsp;&nbsp;';

							if (auth()->user()->can('make-payment'))
							{
								$button .= '<button type="button" name="payment" id="' . $data->id . '" class="generate_payment btn btn-secondary btn-sm" title="Generate Payment"><i class="fa fa-money"></i></button>';
							}

							return $button;
						} else
						{
							return '';
						}
					})
					->rawColumns(['action', 'status'])
					->make(true);
			}

			return view('salary.pay_list.index', compact('companies', 'selected_date'));
		}

		return abort('403', __('You are not authorized'));
	}


    // Details
	public function paySlip(Request $request)
	{
		$month_year = $request->filter_month_year;
		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date = date('Y-m-d', strtotime('last day of ' . $month_year));

		$employee = Employee::with(['salaryBasic' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'allowances' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'commissions'=> function ($query) use ($first_date)
            {
                $query->where('first_date', $first_date);
            },
			'loans'=> function ($query) use ($first_date)
            {
                $query->where('first_date','<=', $first_date)
                ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
            },
			'deductions'=> function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'otherPayments'=> function ($query) use ($first_date)
			{
				$query->where('first_date', $first_date);
			},
			'overtimes'=> function ($query) use ($month_year)
			{
				$query->where('month_year', $month_year);
			},
			'designation', 'department', 'user', 'officeShift',
			'employeeAttendance' => function ($query) use ($first_date, $last_date){
				$query->whereBetween('attendance_date', [$first_date, $last_date]);
			}])
			->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount', 'designation_id', 'department_id', 'joining_date','overtime_allowed','required_hours_per_day','office_shift_id')
			->findOrFail($request->id);

		//payslip_type && salary_basic
		foreach ($employee->salaryBasic as $salaryBasic) {
			if($salaryBasic->first_date <= $first_date){
				$basic_salary = $salaryBasic->basic_salary;
				$payslip_type = $salaryBasic->payslip_type;
			}
		}

        //Pension Amount
        if ($employee->pension_type=="percentage") {
            $pension_amount =  ($basic_salary * $employee->pension_amount) /100.00;
        } else {
            $pension_amount = $employee->pension_amount;
        }


        $type          = "getArray";
        $allowances    = $this->allowances($employee, $first_date, $type);
        $deductions    = $this->deductions($employee, $first_date, $type);
		$data = [];
		$data['basic_salary'] = $basic_salary;
		$data['basic_total']  = $basic_salary;
		$data['allowances']   = $allowances;
		$data['commissions']  = $employee->commissions;
		$data['loans']        = $employee->loans;
		$data['deductions']   = $deductions;
		$data['overtimes']    = $employee->overtimes;
		$data['other_payments'] = $employee->otherPayments;
		$data['pension_type']   = $employee->pension_type;
        $data['pension_amount'] = $pension_amount;

		$data['employee_id']          = $employee->id;
		$data['employee_full_name']   = $employee->full_name;
		$data['employee_designation'] = $employee->designation->designation_name ?? '';
		$data['employee_department']  = $employee->department->department_name ?? '';
		$data['employee_join_date']   = $employee->joining_date;
		$data['employee_username']    = $employee->user->username;
		$data['employee_profile_photo']          = $employee->user->profile_photo ?? '';

		$data['payslip_type'] = $payslip_type;

		if ($payslip_type == 'Hourly')
		{
			$total = 0;
			$total_hours_worked = $this->totalWorkedHours($employee);
			$data['monthly_worked_hours'] = $total_hours_worked;
			//formatting in hour:min and separating them
			sscanf($total_hours_worked, '%d:%d', $hour, $min);
			//converting in minute
			$total += $hour * 60 + $min;

			$data['monthly_worked_amount'] = ($basic_salary / 60) * $total;

			$data['basic_total'] = $data['monthly_worked_amount'];
		}

		return response()->json(['data' => $data]);
	}

	public function payslipGenerateInfoShow(Request $request)
	{
		// Validate and sanitize input
		$month_year = $request->filter_month_year ?? now()->format('F-Y');
		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date = date('Y-m-d', strtotime('last day of ' . $month_year));

		$employee = Employee::with(['salaryBasic' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'allowances' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'commissions'=> function ($query) use ($first_date)
            {
                $query->where('first_date', $first_date);
            },
			'loans'=> function ($query) use ($first_date)
            {
                $query->where('first_date','<=', $first_date)
                ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
            },
			'deductions' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'otherPayments'=> function ($query) use ($first_date)
			{
				$query->where('first_date', $first_date);
			},
			'overtimes'=> function ($query) use ($month_year)
			{
				$query->where('month_year', $month_year);
			},
			'designation', 'department', 'user', 'officeShift',
			'employeeAttendance' => function ($query) use ($first_date, $last_date){
				$query->whereBetween('attendance_date', [$first_date, $last_date]);
			}])
			->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type', 'designation_id', 'department_id', 'joining_date','pension_type','pension_amount','overtime_allowed','required_hours_per_day','office_shift_id')
			->findOrFail($request->id);


		//payslip_type & basic_salary
		$basic_salary = 0;
		$payslip_type = 'Monthly';
		foreach ($employee->salaryBasic as $salaryBasic) {
			if($salaryBasic->first_date <= $first_date)
			{
				$basic_salary = (float)$salaryBasic->basic_salary;
				$payslip_type = $salaryBasic->payslip_type;
			}
		}

        //Pension Amount
        if ($employee->pension_type=="percentage") {
            $pension_amount = ($basic_salary * (float)$employee->pension_amount) / 100;
        } else {
            $pension_amount = (float)$employee->pension_amount;
        }

		$type              = "getAmount";
        $allowance_amount  = (float)$this->allowances($employee, $first_date, $type);
        $deduction_amount  = (float)$this->deductions($employee, $first_date, $type);

		$data = [];
		$data['employee']         = $employee->id;
		$data['basic_salary']     = $basic_salary;
		$data['total_allowance']  = $allowance_amount;
		$data['total_commission'] = $this->safeSum($employee->commissions, 'commission_amount');
		$data['monthly_payable']  = $this->safeSum($employee->loans, 'monthly_payable');
		$data['amount_remaining'] = $this->safeSum($employee->loans, 'amount_remaining');
		// Calculate actual overtime and late deductions from attendance data with error handling
		try {
			// Only calculate if we have valid basic salary and month year
			if ($basic_salary > 0 && !empty($month_year)) {
				$attendanceData = $this->calculateAttendanceDeductionsAndOvertime($employee, $basic_salary, $month_year);
				$data['total_overtime']   = isset($attendanceData['overtime_pay']) ? (float)$attendanceData['overtime_pay'] : 0;
				$data['late_deductions']  = isset($attendanceData['late_deductions']) ? (float)$attendanceData['late_deductions'] : 0;
				$data['overtime_hours']   = isset($attendanceData['overtime_hours']) ? (float)$attendanceData['overtime_hours'] : 0;
			} else {
				throw new \Exception('Invalid basic salary or month year');
			}
		} catch (\Exception $e) {
			// Fallback to original method if attendance calculation fails
			$data['total_overtime']   = $this->safeSum($employee->overtimes, 'overtime_amount');
			$data['late_deductions']  = 0;
			$data['overtime_hours']   = 0;
			\Log::warning('Attendance calculation failed for employee ' . $employee->id . ': ' . $e->getMessage());
		}

		// Total deductions includes both regular deductions and late deductions
		$data['total_deduction']  = (float)$deduction_amount + (float)($data['late_deductions'] ?? 0);

		$data['total_other_payment'] = $this->safeSum($employee->otherPayments, 'other_payment_amount');
		$data['payslip_type']     = $payslip_type;
		$data['pension_amount']   = $pension_amount;

		if ($payslip_type == 'Monthly')
		{
			// $data['total_salary'] = $this->totalSalary($employee); //will be deleted----
			$data['total_salary'] = $this->totalSalary($employee, $payslip_type, $basic_salary, $allowance_amount, $deduction_amount, $pension_amount, 1, $month_year);
		} else
		{
			$total = 0;
			$total_hours = $this->totalWorkedHours($employee);
			if (!empty($total_hours) && preg_match('/^\d+:\d+$/', $total_hours)) {
				sscanf($total_hours, '%d:%d', $hour, $min);
				//converting in minute
				$total += (int)$hour * 60 + (int)$min;
			}
			$data['total_hours'] = $total_hours;
			$data['worked_amount'] = ((float)$data['basic_salary'] / 60) * (float)$total;
			$data['total_salary'] = $this->totalSalary($employee, $payslip_type, $basic_salary, $allowance_amount, $deduction_amount, $pension_amount, $total, $month_year);
		}
		return response()->json(['data' => $data]);
	}


	public function payEmployee($id, Request $request)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('make-payment'))
		{
            // Convert month_year format to standardized format
            $month_year_input = $request->month_year;

            // Check if it's in DD-MM-YYYY format and convert to F-Y format
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $month_year_input)) {
                // Convert DD-MM-YYYY to F-Y format
                $date_parts = explode('-', $month_year_input);
                $day = $date_parts[0];
                $month = $date_parts[1];
                $year = $date_parts[2];
                $standardized_month_year = date('F-Y', mktime(0, 0, 0, $month, 1, $year));
            } else {
                // Assume it's already in the correct format
                $standardized_month_year = $month_year_input;
            }

			$first_date = date('Y-m-d', strtotime('first day of ' . $standardized_month_year));

			// Check if payslip already exists for this employee and month
			$existing_payslip = Payslip::where('employee_id', $id)
				->where('month_year', $standardized_month_year)
				->first();

			if ($existing_payslip) {
				return response()->json([
					'error' => __('Salary for this month has already been paid. Payslip #') . $existing_payslip->payslip_number . __(' exists.')
				]);
			}

			DB::beginTransaction();
				try
				{
					$employee = Employee::with(['allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
                        'loans'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date','<=', $first_date)
                            ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'deductions' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
						'overtimes'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						}])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','company_id')
						->findOrFail($id);


                    $type          = "getArray";
                    $allowances    = $this->allowances($employee, $first_date, $type); //getArray
                    $deductions    = $this->deductions($employee, $first_date, $type);

					// Calculate attendance-based overtime
					$calculated_overtime = [];
					try {
						$attendanceData = $this->calculateAttendanceDeductionsAndOvertime($employee, $request->basic_salary, $standardized_month_year);
						if ($attendanceData['overtime_hours'] > 0) {
							$calculated_overtime[] = [
								'overtime_title' => 'Attendance Overtime',
								'no_of_days' => date('t', strtotime($first_date)), // days in month
								'overtime_hours' => $attendanceData['overtime_hours'],
								'overtime_rate' => round(($request->basic_salary / 26 / ($employee->required_hours_per_day ?: 9)) * 2, 2), // double pay rate
								'overtime_amount' => $attendanceData['overtime_pay']
							];
						}
					} catch (\Exception $e) {
						\Log::warning('Failed to calculate attendance overtime for payslip: ' . $e->getMessage());
						// Fallback to existing overtime records
						$calculated_overtime = $employee->overtimes->toArray();
					}

					$data = [];
					$data['payslip_key']    = Str::random('20');
					$data['payslip_number'] = mt_rand(1000000000,9999999999);
					$data['payment_type']   = $request->payslip_type;
					$data['basic_salary']   = $request->basic_salary;
					$data['allowances']     = $allowances;
					$data['commissions']    = $employee->commissions;
					$data['loans']          = $employee->loans;
					$data['deductions']     = $deductions;
					$data['overtimes']      = $calculated_overtime;
					$data['other_payments'] = $employee->otherPayments;
					$data['month_year']     = $standardized_month_year;
					$data['net_salary']     = $request->net_salary;
					$data['status']         = 1;
					$data['employee_id']    = $employee->id;
					$data['hours_worked']   = is_numeric($request->worked_hours) ? (int)$request->worked_hours : 0;
					$data['pension_type']   = $employee->pension_type;
					$data['pension_amount'] = $request->pension_amount;
					$data['company_id']     = $employee->company_id;

					if ($data['payment_type'] == NULL) { //No Need This Line
						return response()->json(['payment_type_error' => __('Please select a payslip-type for this employee.')]);
					}

					$account_balance = DB::table('finance_bank_cashes')->where('id', config('variable.account_id'))->pluck('account_balance')->first();

					if ((int)$account_balance < (int)$request->net_salary)
					{
						return response()->json(['error' => 'requested balance is less then available balance']);
					}

					$new_balance = (int)$account_balance - (int)$request->net_salary;

					$finance_data = [];

					$finance_data['account_id'] = config('variable.account_id');
					$finance_data['amount'] = $request->net_salary;
					$finance_data ['expense_date'] = now()->format(env('Date_Format'));
					$finance_data ['expense_reference'] = trans('file.Payroll');


					FinanceBankCash::whereId($finance_data['account_id'])->update(['account_balance' => $new_balance]);

					$Expense = FinanceTransaction::create($finance_data);

					$finance_data['id'] = $Expense->id;

					FinanceExpense::create($finance_data);

					if ($employee->loans)
					{
						foreach ($employee->loans as $loan)
						{
							if($loan->time_remaining == '0')
							{
								$amount_remaining = 0;
								$time_remaining   = 0;
								$monthly_payable  = 0;
							}
							else
							{
								$amount_remaining = (int) $loan->amount_remaining - (int) $loan->monthly_payable;
								$time_remaining   = (int) $loan->time_remaining - 1;
								$monthly_payable  = $amount_remaining !=0 ? $loan->monthly_payable : 0;
							}
							SalaryLoan::whereId($loan->id)->update(['amount_remaining' => $amount_remaining, 'time_remaining' => $time_remaining,
								'monthly_payable' => $monthly_payable]);
						}
						$employee_loan = Employee::with('loans:id,employee_id,loan_title,loan_amount,time_remaining,amount_remaining,monthly_payable')
							->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type')
							->findOrFail($id);
						$data['loans'] = $employee_loan->loans;
					}
					Payslip::create($data);

					DB::commit();

				} catch (Exception $e)
				{
					DB::rollback();
					return response()->json(['error' => $e->getMessage()]);
				} catch (Throwable $e)
				{
					DB::rollback();
					return response()->json(['error' => $e->getMessage()]);
				}

				return response()->json(['success' => __('Data Added successfully.')]);
		}
		return response()->json(['success' => __('You are not authorized')]);
	}


	//--- Updated ----
	public function payBulk(Request $request)
	{
		$logged_user = auth()->user();
		if ($logged_user->can('make-bulk_payment'))
		{
			if (request()->ajax())
			{
                // Convert month_year format to standardized format
                $month_year_input = $request->month_year;
                \Log::info('Bulk Payment: Original month_year input', ['month_year' => $month_year_input]);

                // Check if it's in DD-MM-YYYY format and convert to F-Y format
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $month_year_input)) {
                    // Convert DD-MM-YYYY to F-Y format
                    $date_parts = explode('-', $month_year_input);
                    $day = $date_parts[0];
                    $month = $date_parts[1];
                    $year = $date_parts[2];
                    $standardized_month_year = date('F-Y', mktime(0, 0, 0, $month, 1, $year));
                } else {
                    // Assume it's already in the correct format
                    $standardized_month_year = $month_year_input;
                }

                \Log::info('Bulk Payment: Standardized month_year', ['standardized' => $standardized_month_year]);

                $first_date = date('Y-m-d', strtotime('first day of ' . $standardized_month_year));
				$employeeArrayId = $request->all_checkbox_id;
				//$employeesId = Employee::whereIntegerInRaw('id',$employeeArrayId)->whereIntegerNotInRaw('id',$paid_employee)->pluck('id');

				if (!empty($request->filter_company && $request->filter_department)) //No Need
				{
					$employees = Employee::with(['salaryBasic' => function ($query)
                        {
                            $query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
                        'loans'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date','<=', $first_date)
                            ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'deductions' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
						'overtimes'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						}])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','company_id')
						->where('company_id', $request->filter_company)
						->where('department_id', $request->filter_department)
						->whereIntegerInRaw('id', $employeeArrayId)
                        ->where('is_active',1)->where('exit_date',NULL)
						->get();
				}
                elseif (!empty($request->filter_company)) //No Need
				{
					$employees = Employee::with(['salaryBasic' => function ($query)
                        {
                            $query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
                        'loans'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date','<=', $first_date)
                            ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'deductions' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
						'overtimes'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						}])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','company_id')
						->where('company_id', $request->filter_company)
						->whereIntegerInRaw('id', $employeeArrayId)
                        ->where('is_active',1)->where('exit_date',NULL)
						->get();
				}
                else
				{
					$employees = Employee::with(['salaryBasic' => function ($query)
                        {
                            $query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
                        'loans'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date','<=', $first_date)
                            ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'deductions' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
						'overtimes'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						}])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','company_id')
						->whereIntegerInRaw('id', $employeeArrayId)
                        ->where('is_active',1)->where('exit_date',NULL)
						->get();
				}


				DB::beginTransaction();
					try
					{
						$total_sum = 0;
						$processed_count = 0;
						$skipped_count = 0;
						$skipped_employees = [];

						foreach ($employees as $employee)
						{
							// Check if payslip already exists for this employee and month
							$existing_payslip = Payslip::where('employee_id', $employee->id)
								->where('month_year', $standardized_month_year)
								->first();

							if ($existing_payslip) {
								$skipped_count++;
								$skipped_employees[] = $employee->first_name . ' ' . $employee->last_name;
								\Log::warning('Bulk Payment: Skipping employee ' . $employee->id . ' - salary already paid for ' . $standardized_month_year);
								continue; // Skip this employee
							}

                            //payslip_type & basic_salary
                            foreach ($employee->salaryBasic as $salaryBasic) {
                                if($salaryBasic->first_date <= $first_date){
                                    $payslip_type = $salaryBasic->payslip_type;
                                    $basicsalary = $salaryBasic->basic_salary;
                                }
                            }

                            //Pension Amount
                            \Log::info('DEBUG: Pension calculation', [
                                'employee_id' => $employee->id,
                                'pension_type' => $employee->pension_type,
                                'pension_amount_raw' => $employee->pension_amount,
                                'pension_amount_type' => gettype($employee->pension_amount),
                                'basicsalary' => $basicsalary,
                                'basicsalary_type' => gettype($basicsalary)
                            ]);

                            if ($employee->pension_type=="percentage") {
                                $pension_raw = (float)$employee->pension_amount;
                                $basic_raw = (float)$basicsalary;

                                if ($basic_raw > 0) {
                                    $pension_amount = ($basic_raw * $pension_raw) / 100;
                                } else {
                                    \Log::error('Invalid basic salary for pension calculation', [
                                        'employee_id' => $employee->id,
                                        'basicsalary' => $basicsalary
                                    ]);
                                    $pension_amount = 0;
                                }
                            } else {
                                $pension_amount = (float)($employee->pension_amount ?? 0);
                            }

                            \Log::info('DEBUG: Pension calculated', [
                                'employee_id' => $employee->id,
                                'pension_amount' => $pension_amount,
                                'pension_amount_type' => gettype($pension_amount)
                            ]);

                            $type1          = "getArray";
                            $allowances    = $this->allowances($employee, $first_date, $type1); //getArray
                            $deductions    = $this->deductions($employee, $first_date, $type1);

                            $type2             = "getAmount";
                            $allowance_amount  = $this->allowances($employee, $first_date, $type2);
                            $deduction_amount  = $this->deductions($employee, $first_date, $type2);


							//Net Salary
                            // Initialize total_hours to prevent null values
                            $total_hours = '0:0';

                            if ($payslip_type == 'Monthly'){
                                $net_salary = $this->totalSalary($employee, $payslip_type, $basicsalary, $allowance_amount, $deduction_amount, $pension_amount, 1, $request->month_year);


                                //New- just store work hours, not calculte with salary
                                $total = 0;
                                $total_hours = $this->totalWorkedHours($employee);

                                \Log::info('DEBUG: totalWorkedHours result', [
                                    'employee_id' => $employee->id,
                                    'total_hours' => $total_hours,
                                    'total_hours_type' => gettype($total_hours),
                                    'is_string' => is_string($total_hours),
                                    'is_null' => is_null($total_hours)
                                ]);

                                // Validate total_hours format before sscanf
                                if (!is_string($total_hours) || !preg_match('/^\d+:\d+$/', $total_hours)) {
                                    \Log::error('Invalid total_hours format', [
                                        'employee_id' => $employee->id,
                                        'total_hours' => $total_hours,
                                        'expected_format' => 'HH:MM'
                                    ]);
                                    $total_hours = '0:0'; // Default fallback
                                }

                                $hour = 0;
                                $min = 0;
                                $parsed_count = sscanf($total_hours, '%d:%d', $hour, $min);

                                \Log::info('DEBUG: sscanf results', [
                                    'employee_id' => $employee->id,
                                    'parsed_count' => $parsed_count,
                                    'hour' => $hour,
                                    'min' => $min,
                                    'hour_type' => gettype($hour),
                                    'min_type' => gettype($min)
                                ]);

                                // Ensure numeric values
                                $hour = (int)($hour ?? 0);
                                $min = (int)($min ?? 0);

                                //converting in minute
                                $total += $hour * 60 + $min;
                            }
                            else{
                                $total = 0;
                                $total_hours = $this->totalWorkedHours($employee);

                                \Log::info('DEBUG: totalWorkedHours result (hourly)', [
                                    'employee_id' => $employee->id,
                                    'total_hours' => $total_hours,
                                    'total_hours_type' => gettype($total_hours)
                                ]);

                                // Validate total_hours format before sscanf
                                if (!is_string($total_hours) || !preg_match('/^\d+:\d+$/', $total_hours)) {
                                    \Log::error('Invalid total_hours format (hourly)', [
                                        'employee_id' => $employee->id,
                                        'total_hours' => $total_hours,
                                        'expected_format' => 'HH:MM'
                                    ]);
                                    $total_hours = '0:0'; // Default fallback
                                }

                                $hour = 0;
                                $min = 0;
                                $parsed_count = sscanf($total_hours, '%d:%d', $hour, $min);

                                \Log::info('DEBUG: sscanf results (hourly)', [
                                    'employee_id' => $employee->id,
                                    'parsed_count' => $parsed_count,
                                    'hour' => $hour,
                                    'min' => $min
                                ]);

                                // Ensure numeric values
                                $hour = (int)($hour ?? 0);
                                $min = (int)($min ?? 0);

                                //converting in minute
                                $total += $hour * 60 + $min;
                                $net_salary = $this->totalSalary($employee, $payslip_type, $basicsalary, $allowance_amount, $deduction_amount, $pension_amount, $total, $request->month_year);
                            }

							// Calculate attendance-based overtime for bulk payment
							$calculated_overtime = [];
							try {
								$attendanceData = $this->calculateAttendanceDeductionsAndOvertime($employee, $basicsalary, $standardized_month_year);
								if ($attendanceData['overtime_hours'] > 0) {
									$calculated_overtime[] = [
										'overtime_title' => 'Attendance Overtime',
										'no_of_days' => date('t', strtotime($first_date)), // days in month
										'overtime_hours' => $attendanceData['overtime_hours'],
										'overtime_rate' => round(($basicsalary / 26 / ($employee->required_hours_per_day ?: 9)) * 2, 2), // double pay rate
										'overtime_amount' => $attendanceData['overtime_pay']
									];
								}
							} catch (\Exception $e) {
								\Log::warning('Failed to calculate attendance overtime for bulk payslip: ' . $e->getMessage());
								// Fallback to existing overtime records
								$calculated_overtime = $employee->overtimes->toArray();
							}

							$data = [];
							$data['payslip_key']    = Str::random('20');
							$data['payslip_number'] = mt_rand(1000000000,9999999999);
							$data['payment_type']   = $payslip_type;
							$data['basic_salary']   = $basicsalary; //
							$data['allowances']     = $allowances;
							$data['commissions']    = $employee->commissions;
							$data['loans']          = $employee->loans;
							$data['deductions']     = $deductions;
							$data['overtimes']      = $calculated_overtime;
							$data['other_payments'] = $employee->otherPayments;
							$data['month_year']     = $standardized_month_year;
							$data['net_salary']     = $net_salary;
							$data['status']         = 1;
							$data['employee_id']    = $employee->id;
                            // Convert time format to minutes for database storage
                            $hours_in_minutes = 0;
                            if (!empty($total_hours) && is_string($total_hours) && preg_match('/^\d+:\d+$/', $total_hours)) {
                                $hour = 0;
                                $min = 0;
                                sscanf($total_hours, '%d:%d', $hour, $min);
                                $hours_in_minutes = ((int)$hour * 60) + (int)$min;
                            }
                            $data['hours_worked']   = $hours_in_minutes; //stored as total minutes
                            $data['pension_type']   = $employee->pension_type;
                            $data['pension_amount'] = $pension_amount;
                            $data['company_id']     = $employee->company_id;

							\Log::info('DEBUG: Total sum calculation', [
								'employee_id' => $employee->id,
								'current_total_sum' => $total_sum,
								'net_salary' => $net_salary,
								'net_salary_type' => gettype($net_salary),
								'total_sum_type' => gettype($total_sum)
							]);

							// Ensure both values are numeric before addition
							$total_sum = (float)$total_sum + (float)$net_salary;

							if ($employee->loans)
							{
								foreach ($employee->loans as $loan)
								{
									\Log::info('DEBUG: Loan calculation', [
										'employee_id' => $employee->id,
										'loan_id' => $loan->id,
										'time_remaining' => $loan->time_remaining,
										'time_remaining_type' => gettype($loan->time_remaining),
										'amount_remaining' => $loan->amount_remaining,
										'amount_remaining_type' => gettype($loan->amount_remaining),
										'monthly_payable' => $loan->monthly_payable,
										'monthly_payable_type' => gettype($loan->monthly_payable)
									]);

									if($loan->time_remaining == '0')
									{
										$amount_remaining = 0;
										$time_remaining = 0;
										$monthly_payable = 0;
									}
									else
									{
										// Ensure all values are numeric for calculations
										$current_amount = (float)($loan->amount_remaining ?? 0);
										$monthly_pay = (float)($loan->monthly_payable ?? 0);
										$current_time = (int)($loan->time_remaining ?? 0);

										$amount_remaining = $current_amount - $monthly_pay;
										$time_remaining = $current_time - 1;
										$monthly_payable = $amount_remaining != 0 ? $monthly_pay : 0;
									}

									\Log::info('DEBUG: Loan calculation results', [
										'employee_id' => $employee->id,
										'loan_id' => $loan->id,
										'calculated_amount_remaining' => $amount_remaining,
										'calculated_time_remaining' => $time_remaining,
										'calculated_monthly_payable' => $monthly_payable
									]);
									SalaryLoan::whereId($loan->id)->update(['amount_remaining' => $amount_remaining, 'time_remaining' => $time_remaining,
										'monthly_payable' => $monthly_payable]);
								}
								$employee_loan = Employee::with('loans:id,employee_id,loan_title,loan_amount,time_remaining,amount_remaining,monthly_payable')
									->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type')
									->findOrFail($employee->id);
								$data['loans'] = $employee_loan->loans;
							}

							if ($data['payment_type'] == NULL) { //New
								return response()->json(['payment_type_error' => __('Please select payslip-type for the employees.')]);
							}
							Payslip::create($data);
							$processed_count++;
						}


						$account_balance = DB::table('finance_bank_cashes')->where('id', config('variable.account_id'))->pluck('account_balance')->first();

						\Log::info('DEBUG: Account balance calculation', [
							'account_balance' => $account_balance,
							'account_balance_type' => gettype($account_balance),
							'total_sum' => $total_sum,
							'total_sum_type' => gettype($total_sum)
						]);

						// Ensure both values are numeric for comparison and calculation
						$balance_numeric = (float)($account_balance ?? 0);
						$total_sum_numeric = (float)$total_sum;

						if ($balance_numeric < $total_sum_numeric)
						{
							throw new Exception("requested balance is less then available balance");
						}

						$new_balance = $balance_numeric - $total_sum_numeric;

						$finance_data = [];

						$finance_data['account_id'] = config('variable.account_id');
						$finance_data['amount'] = $total_sum;
						$finance_data ['expense_date'] = now()->format(env('Date_Format'));
						$finance_data ['expense_reference'] = trans('file.Payroll');


						FinanceBankCash::whereId($finance_data['account_id'])->update(['account_balance' => $new_balance]);

						$Expense = FinanceTransaction::create($finance_data);

						$finance_data['id'] = $Expense->id;

						FinanceExpense::create($finance_data);

						DB::commit();
					} catch (Exception $e)
					{
						DB::rollback();
						return response()->json(['error' =>  $e->getMessage()]);
					} catch (Throwable $e)
					{
						DB::rollback();
						return response()->json(['error' => $e->getMessage()]);
					}

					// Build success message with payment details
					$success_message = __('Bulk Payment Completed') . '! ' . $processed_count . __(' employee(s) paid successfully.');

					if ($skipped_count > 0) {
						$success_message .= ' ' . $skipped_count . __(' employee(s) were skipped as they were already paid for this month') . ': ' . implode(', ', $skipped_employees) . '.';
					}

					return response()->json(['success' => $success_message]);
			}
		}

		return response()->json(['error' => __('Error')]);
	}

    protected function allowances($employee, $first_date, $type)
    {
        if ($type=="getArray") {
            $allowances = array(); // Initialize outside the loops

            if (!$employee->allowances->isEmpty()) {
                foreach($employee->allowances as $item) {
                    if($item->first_date <= $first_date){
                        foreach($employee->allowances as $key => $value) {
                            if($value->first_date <= $first_date){
                                if ($item->first_date == $value->first_date) {
                                    $allowances[] =  $employee->allowances[$key];
                                }
                            }
                        }
                        break; // Exit after finding the first valid date to avoid duplicates
                    }
                }
            }
            return $allowances;
        }
        elseif ($type=="getAmount") {
            $allowance_amount = 0;
            if (!$employee->allowances->isEmpty()) {
                foreach($employee->allowances as $item) {
                    if($item->first_date <= $first_date){
                        // $allowance_amount = SalaryAllowance::where('month_year',$item->month_year)->where('employee_id',$item->employee_id)->sum('allowance_amount');
                        $allowance_amount = 0;
                        foreach($employee->allowances as $value) {
                            if($value->first_date <= $first_date){
                                if ($item->first_date == $value->first_date) {
                                    $allowance_amount += $value->allowance_amount;
                                }
                            }
                        }
                    }
                }
            }

            return $allowance_amount;
        }

    }

    protected function deductions($employee, $first_date ,$type)
    {
        if ($type=="getAmount") {
            $deduction_amount = 0;
            if (!$employee->deductions->isEmpty()) {
                foreach($employee->deductions as $item) {
                    if($item->first_date <= $first_date){
                        $deduction_amount = 0;
                        foreach($employee->deductions as $value) {
                            if($value->first_date <= $first_date){
                                if ($item->first_date == $value->first_date) {
                                    $deduction_amount += $value->deduction_amount;
                                }
                            }
                        }
                    }
                }
            }
            return $deduction_amount;
        }
        elseif($type=="getArray") {
            $deductions = array(); // Initialize outside the loops

            if (!$employee->deductions->isEmpty()) {
                foreach($employee->deductions as $item) {
                    if($item->first_date <= $first_date){
                        foreach($employee->deductions as $key => $value) {
                            if($value->first_date <= $first_date){
                                if ($item->first_date == $value->first_date) {
                                    $deductions[] =  $employee->deductions[$key];
                                }
                            }
                        }
                        break; // Exit after finding the first valid date to avoid duplicates
                    }
                }
            }
            return $deductions;
        }
    }
}


