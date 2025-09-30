<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\company;
use App\Models\Employee;
use App\Models\Holiday;
use App\Imports\AttendancesImport;
use App\Imports\AttendancesImportDevice;
use App\Imports\AttendancesCsvImport;
use App\Services\AttendanceProcessor;
use App\Services\OvertimeCalculationService;
use App\Services\EmployeeShiftManagementService;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

use App\Http\traits\MonthlyWorkedHours;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller {

	use MonthlyWorkedHours;

	public $date_attendance = [];
	public $date_range = [];
	public $work_days = 0;

	public function index(Request $request)
	{
		$logged_user = auth()->user();
		//checking if date is selected else date is current
		// if ($logged_user->can('view-attendance'))
		// {
			$selected_date = Carbon::parse($request->filter_month_year)->format('Y-m-d') ?? now()->format('Y-m-d');

			$day = strtolower(Carbon::parse($request->filter_month_year)->format('l')) . '_in' ?? strtolower(now()->format('l')) . '_in';


			if (request()->ajax())
			{
				//employee attendance of selected date

				// if($logged_user->role_users_id != 1){
				if(!($logged_user->can('daily-attendances'))){ //Correction
					$employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($selected_date)
					{
						$query->where('attendance_date', $selected_date);
					},
						'officeShift',
						'company:id,company_name',
						'employeeLeave' => function ($query) use ($selected_date)
						{
							$query->where('start_date', '<=', $selected_date)
								->where('end_date', '>=', $selected_date);
						}]
					)
					->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id')
					->where('joining_date', '<=', $selected_date)
					->where('id', '=', $logged_user->id)
                    ->where('is_active',1)
                    ->where('exit_date',NULL)
					->get();
				}
				else{
					$employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($selected_date)
					{
						$query->where('attendance_date', $selected_date);
					},
						'officeShift',
						'company:id,company_name',
						'employeeLeave' => function ($query) use ($selected_date)
						{
							$query->where('start_date', '<=', $selected_date)
								->where('end_date', '>=', $selected_date);
						}]
					)
					->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id')
					->where('joining_date', '<=', $selected_date)
                    ->where('is_active',1)
                    ->where('exit_date',NULL)
					->get();
				}



				$holidays = Holiday::select('id', 'company_id', 'start_date', 'end_date', 'is_publish')
					->where('start_date', '<=', $selected_date)
					->where('end_date', '>=', $selected_date)
					->where('is_publish', '=', 1)->first();


				return datatables()->of($employee)
					->setRowId(function ($employee)
					{
						return $employee->id;
					})
					->addColumn('employee_name', function ($employee)
					{
						return $employee->full_name;
					})
					->addColumn('company', function ($employee)
					{
						return $employee->company->company_name;
					})
					->addColumn('attendance_date', function ($employee) use ($selected_date)
					{
						//if there is no employee attendance
						if ($employee->employeeAttendance->isEmpty())
						{
							return Carbon::parse($selected_date)->format(env('Date_Format'));
						} else
						{
							//if there are employee attendance,get the first record
							$attendance_row = $employee->employeeAttendance->first();

							return $attendance_row->attendance_date;
						}
					})
					->addColumn('attendance_status', function ($employee) use ($holidays, $day)
					{
						//if there are employee attendance,get the first record
						if ($employee->employeeAttendance->isEmpty())
						{
							if (is_null($employee->officeShift->$day ?? null) || ($employee->officeShift->$day == ''))
							{
								return __('Off Day');
							}

							if ($holidays)
							{
								if ($employee->company_id == $holidays->company_id)
								{
									return trans('file.Holiday');
								}
							}


							if ($employee->employeeLeave->isEmpty())
							{
								return trans('file.Absent');
							}

							return __('On leave');

						} else
						{
							$attendance_row = $employee->employeeAttendance->first();

							return $attendance_row->attendance_status;
						}
					})
					->addColumn('clock_in', function ($employee)
					{
						if ($employee->employeeAttendance->isEmpty())
						{
							return '---';
						} else
						{
							$attendance_row = $employee->employeeAttendance->first();

							return $attendance_row->clock_in;
						}
					})
					->addColumn('clock_out', function ($employee)
					{
						if ($employee->employeeAttendance->isEmpty())
						{
							return '---';
						} else
						{
							$attendance_row = $employee->employeeAttendance->last();

							return $attendance_row->clock_out;
						}
					})
					->addColumn('time_late', function ($employee)
					{
						if ($employee->employeeAttendance->isEmpty())
						{
							return '---';
						} else
						{
							$attendance_row = $employee->employeeAttendance->first();

							return $attendance_row->time_late;
						}
					})
					->addColumn('early_leaving', function ($employee)
					{
						if ($employee->employeeAttendance->isEmpty())
						{
							return '---';
						} else
						{
							$attendance_row = $employee->employeeAttendance->last();

							return $attendance_row->early_leaving;
						}
					})
					->addColumn('overtime', function ($employee)
					{
						if ($employee->employeeAttendance->isEmpty())
						{
							return '---';
						}
                        else
						{
							$total = 0;
							foreach ($employee->employeeAttendance as $attendance_row)
							{
								sscanf($attendance_row->overtime, '%d:%d', $hour, $min);
								$total += $hour * 60 + $min;
							}
							if ($h = floor($total / 60))
							{
								$total %= 60;
							}

							return sprintf('%02d:%02d', $h, $total);
						}
					})
					->addColumn('total_work', function ($employee)
					{
						if ($employee->employeeAttendance->isEmpty())
						{
							return '---';
						}
                        else
						{
							$total = 0;
							foreach ($employee->employeeAttendance as $attendance_row)
							{
								sscanf($attendance_row->total_work, '%d:%d', $hour, $min);
								$total += $hour * 60 + $min;
							}
							if ($h = floor($total / 60))
							{
								$total %= 60;
							}
							return sprintf('%02d:%02d', $h, $total);
						}
					})
					->addColumn('total_rest', function ($employee)
					{
						if ($employee->employeeAttendance->isEmpty())
						{
							return '---';
						}
                        else
						{
							$total = 0;
							foreach ($employee->employeeAttendance as $attendance_row)
							{
								//formatting in hour:min and separating them
								sscanf($attendance_row->total_rest, '%d:%d', $hour, $min);
								//converting in minute
								$total += $hour * 60 + $min;
							}
							// if minute is greater than hour then $h= hour
							if ($h = floor($total / 60))
							{
								$total %= 60;
							}
							//returning back to hour:minute format
							return sprintf('%02d:%02d', $h, $total);
						}
					})
					->rawColumns(['action'])
					->make(true);
			}

			return view('timesheet.attendance.attendance', compact('selected_date'));
		// }

		return response()->json(['success' => __('You are not authorized')]);
	}


	public function employeeAttendance(Request $request, $id)
	{

		$data = [];

		//current day
		$current_day = now()->format(env('Date_Format'));

		//getting the latest instance of employee_attendance
		$employee_attendance_last = Attendance::where('attendance_date', now()->format('Y-m-d'))
				->where('employee_id', $id)->orderBy('id', 'desc')->first() ?? null;

		//shift in-shift out timing
		try
		{
			$shift_in = new DateTime($request->office_shift_in);
			$shift_out = new DateTime($request->office_shift_out);
			$current_time = new DateTime(now());

		} catch (Exception $e)
		{
			return $e;
		}


		$data['employee_id'] = $id;
		$data['attendance_date'] = $current_day;


		//if employee attendance record was not found
		// FOR CLOCK IN
		if (!$employee_attendance_last)
		{
			// if employee is late (apply 15-minute grace period)
			if ($current_time > $shift_in)
			{
				$data['clock_in'] = $current_time->format('H:i');
                $rawLateMinutes = $shift_in->diff(new DateTime($data['clock_in']))->i + ($shift_in->diff(new DateTime($data['clock_in']))->h * 60);
				// Apply 15-minute grace period rule
				if ($rawLateMinutes > 15) {
					$hours = floor($rawLateMinutes / 60);
					$minutes = $rawLateMinutes % 60;
					$data['time_late'] = sprintf('%02d:%02d', $hours, $minutes);
				} else {
					$data['time_late'] = '00:00'; // Within grace period
				}
			} // if employee is early or on time
			else
			{
                if(env('ENABLE_EARLY_CLOCKIN')!=NULL) {
                    $data['clock_in'] = $current_time->format('H:i');
                }
                else {
				    $data['clock_in'] = $shift_in->format('H:i');
                }
			}

			$data['attendance_status'] = 'present';
			$data['clock_in_out'] = 1;
			$data['clock_in_ip'] = $request->ip();

			// Get the correct shift for this employee on this date
			$shiftService = new EmployeeShiftManagementService();
			$correctShift = $shiftService->getEmployeeShiftForDate($id, $current_day);
			if ($correctShift) {
				$data['office_shift_id'] = $correctShift->id;
			}

			//creating new attendance record
			$attendance = Attendance::create($data);

			// Auto-calculate overtime for this attendance if it's a clock-out
			if (isset($data['clock_out'])) {
				try {
					$overtimeService = app(OvertimeCalculationService::class);
					$overtimeService->processAttendanceOvertime($attendance);
				} catch (\Exception $e) {
					\Log::warning('Failed to calculate overtime for attendance ID ' . $attendance->id . ': ' . $e->getMessage());
				}
			}

			$this->setSuccessMessage(__('Clocked In Successfully'));
			return redirect()->back();
		}
		// if there is a record of employee attendance
		//FOR CLOCK OUT
		//if ($employee_attendance_last)
        else {
			//checking if the employee is not both clocked in + out (1)
			if ($employee_attendance_last->clock_in_out == 1) {
                if ($current_time > $shift_in || env('ENABLE_EARLY_CLOCKIN')!=NULL) {
					$employee_last_clock_in = new DateTime($employee_attendance_last->clock_in);
                    $data['clock_out'] = $current_time->format('H:i');
                    // if employee is early leaving
                    if ($current_time < $shift_out) {
                        $timeDifference = $shift_out->diff(new DateTime($data['clock_out']))->format('%H:%I');
                        $data['early_leaving'] = $timeDifference;
                    }
                    // calculating total work
                    $prev_work = new DateTime($employee_attendance_last->total_work);
                    $total_work = $prev_work->add($employee_last_clock_in->diff(new DateTime($data['clock_out'])));
                    $data['total_work'] = $total_work->format('H:i');

                    // Overtime calculation
                    $duty_time = new DateTime($shift_in->diff($shift_out)->format('%H:%I'));
                    if ($total_work > $duty_time) {
                        $data['overtime'] = $total_work->diff($duty_time)->format('%H:%I');
                    }
                    $data['clock_out_ip'] = $request->ip();
                    $data['clock_in_out'] = 0;
                    //updating record
                    $attendance = Attendance::findOrFail($employee_attendance_last->id);
                    $attendance->update($data);

                    // Auto-calculate overtime for clock-out
                    try {
                        $overtimeService = app(OvertimeCalculationService::class);
                        $overtimeService->processAttendanceOvertime($attendance->fresh());
                    } catch (\Exception $e) {
                        \Log::warning('Failed to calculate overtime for attendance ID ' . $attendance->id . ': ' . $e->getMessage());
                    }
                }
                else {
                    Attendance::whereId($employee_attendance_last->id)->delete();
                }

				$this->setSuccessMessage(__('Clocked Out Successfully'));
				return redirect()->back();
			}
			// if employee is both clocked in + out
			// if ($employee_attendance_last->clock_in_out == 0)
            else {
				$data['clock_in'] = $current_time->format('H:i');
				// last clock out (needed for calculation rest time)
				$employee_last_clock_out = new DateTime($employee_attendance_last->clock_out);
				$data['total_rest'] = $employee_last_clock_out->diff(new DateTime($data['clock_in']))->format('%H:%I');
				$data['total_work'] = $employee_attendance_last->total_work;
				$data['overtime'] = $employee_attendance_last->overtime;
				$data['clock_in_out'] = 1;
                $data['clock_in_ip'] = $request->ip();

				Attendance::whereId($employee_attendance_last->id)->update(['total_work'=> '00:00', 'overtime'=> '00:00']);
				// creating new attendance
				Attendance::create($data);
				$this->setSuccessMessage(__('Clocked In Successfully'));
				return redirect()->back();
			}
		}

		return response()->json(trans('file.Success'));
	}


    public function test($request, $companies, $start_date, $end_date)
    {
        $request->employee_id   = 9;
        $request->company_id    = 1;
        $request->department_id = 1;


        $employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($start_date, $end_date)
        {
            $query->whereBetween('attendance_date', [$start_date, $end_date]);
        },
            'employeeLeave',
            'company:id,company_name',
            'company.companyHolidays'
        ])
        ->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id', 'joining_date')
        ->where('is_active', '=', 1);

        if ($request->employee_id) {
            $employee = $employee->where('id', '=', $request->employee_id)->get();
        }
        elseif ($request->department_id) {
            $employee = $employee->where('department_id', '=', $request->department_id)->get();
        }
        elseif ($request->company_id) {
            $employee = $employee->where('company_id', '=', $request->company_id)->get();
        }

        $begin = new DateTime($start_date);
        $end = new DateTime($end_date);
        $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($begin, $interval, $end);
        $date_range = [];
        foreach ($period as $dt) {
            $date_range[] = $dt->format(env('Date_Format'));
        }
        $emp_attendance_date_range = [];


        foreach ($employee as $key1 => $emp) {
            $all_attendances_array = $emp->employeeAttendance->groupBy('attendance_date')->toArray();
            $leaves = $emp->employeeLeave;
            $shift = $emp->officeShift->toArray();
            $holidays = $emp->company->companyHolidays;
            $joining_date = Carbon::parse($emp->joining_date)->format(env('Date_Format'));
            foreach ($date_range as $key2 => $dt_r) {
                $emp_attendance_date_range[$key1*count($date_range)+$key2]['id'] = $emp->id;
                $emp_attendance_date_range[$key1*count($date_range)+$key2]['employee_name'] = ($key2==0) ? '<strong>'.$emp->full_name.'</strong>' : $emp->full_name;
                $emp_attendance_date_range[$key1*count($date_range)+$key2]['company'] = $emp->company->company_name;
                $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_date'] = Carbon::parse($dt_r)->format(env('Date_Format'));

                //attendance status
                $day = strtolower(Carbon::parse($dt_r)->format('l')) . '_in';
                if (strtotime($dt_r) < strtotime($joining_date))
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('Not Join');
                }
                elseif (empty($shift[$day]))
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('Off Day');
                }
                elseif (array_key_exists($dt_r, $all_attendances_array))
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = trans('file.present');
                }
                else
                {
                    foreach ($leaves as $leave)
                    {
                        // Test Start
                        // $start_date = Carbon::parse($leave->start_date);
                        // $end_date   = Carbon::parse($leave->end_date);
                        // $dateRange  = Carbon::parse($dt_r);

                        $leaveDateTimesStart = strtotime($leave->start_date);
                        $leaveDateTimesEnd   = strtotime($leave->end_date);
                        $dateRange           = strtotime($dt_r);

						return $leaveDateTimesStart;

                        if ($leaveDateTimesStart <= $dateRange){
                            return $dt_r;
                        }
                        // return gettype($start_date);

                        // if ($start_date->lte($dateRange) && $end_date->gte($dateRange)) {
                        //     // $date1 is less than or equal to $date2 || // $date1 is greater than or equal to $date2
                        //     return $dateRange;
                        // }

                        return $dt_r;

                        // Test End

                        if ($leave->start_date <= $dt_r && $leave->end_date >= $dt_r)
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('On Leave');
                        }
                    }
                    foreach ($holidays as $holiday)
                    {
                        if ($holiday->start_date <= $dt_r && $holiday->end_date >= $dt_r)
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('On Holiday');
                        }
                    }
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = trans('Absent');
                }

                //attendance status

                //clock in
                if (array_key_exists($dt_r, $all_attendances_array))
                {
                    $first = current($all_attendances_array[$dt_r])['clock_in'];
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_in'] = $first;
                }
                else
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_in'] = '---';
                }
                //clock in

                //clock out
                if (array_key_exists($dt_r, $all_attendances_array))
                {
                    $last = end($all_attendances_array[$dt_r])['clock_out'];
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_out'] = $last;
                }
                else
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_out'] = '---';
                }
                //clock out

                //time late
                if (array_key_exists($dt_r, $all_attendances_array))
                {
                    $first = current($all_attendances_array[$dt_r])['time_late'];
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['time_late'] = $first;
                } else
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['time_late'] = '---';
                }
                //time late

                //early_leaving
                if (array_key_exists($dt_r, $all_attendances_array))
                {
                    $last = end($all_attendances_array[$dt_r])['early_leaving'];
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['early_leaving'] = $last;
                } else
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['early_leaving'] = '---';
                }
                //early_leaving

                //overtime
                if (array_key_exists($dt_r, $all_attendances_array))
                {
                    $total = 0;
                    foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
                    {
                        sscanf($all_attendance_item['overtime'], '%d:%d', $hour, $min);
                        $total += $hour * 60 + $min;
                    }
                    if ($h = floor($total / 60))
                    {
                        $total %= 60;
                    }
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['overtime'] = sprintf('%02d:%02d', $h, $total);
                } else
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['overtime'] = '---';
                }
                //overtime

                //total_work
                if (array_key_exists($dt_r, $all_attendances_array))
                {
                    $total = 0;
                    foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
                    {
                        sscanf($all_attendance_item['total_work'], '%d:%d', $hour, $min);
                        $total += $hour * 60 + $min;
                    }
                    if ($h = floor($total / 60))
                    {
                        $total %= 60;
                    }
                    $sum_total = 0 + $total;
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_work'] = sprintf('%02d:%02d', $h, $total);
                }
                else
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_work'] = '---';
                }
                //total_work

                //total_rest
                if (array_key_exists($dt_r, $all_attendances_array))
                {
                    $total = 0;
                    foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
                    {
                        //formatting in hour:min and separating them
                        sscanf($all_attendance_item['total_rest'], '%d:%d', $hour, $min);
                        //converting in minute
                        $total += $hour * 60 + $min;
                    }
                    // if minute is greater than hour then $h= hour
                    if ($h = floor($total / 60))
                    {
                        //$total = minute (after excluding hour)
                        $total %= 60;
                    }
                    //returning back to hour:minute format
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_rest'] = sprintf('%02d:%02d', $h, $total);
                } else
                {
                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_rest'] = '---';
                }
                //total_rest
            }
        }
        return 'END';
    }


	// public function dateWiseAttendance(Request $request)
	// {
	// 	$logged_user = auth()->user();

    //     $companies = Company::all('id', 'company_name');
    //     $start_date = Carbon::parse($request->filter_start_date)->format('Y-m-d') ?? '';
    //     $end_date = Carbon::parse($request->filter_end_date)->format('Y-m-d') ?? '';

    //     if (request()->ajax())
    //     {
    //         if (!$request->company_id && !$request->department_id && !$request->employee_id) {
    //             $emp_attendance_date_range = [];
    //         }
    //         else
    //         {
    //             $employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($start_date, $end_date)
    //             {
    //                 $query->whereBetween('attendance_date', [$start_date, $end_date]);
    //             },
    //                 'employeeLeave',
    //                 'company:id,company_name',
    //                 'company.companyHolidays'
    //             ])
    //             ->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id', 'joining_date')
    //             ->where('is_active', '=', 1);

    //             if ($request->employee_id) {
    //                 $employee = $employee->where('id', '=', $request->employee_id)->get();
    //             }
    //             elseif ($request->department_id) {
    //                 $employee = $employee->where('department_id', '=', $request->department_id)->get();
    //             }
    //             elseif ($request->company_id) {
    //                 $employee = $employee->where('company_id', '=', $request->company_id)->get();
    //             }

    //             $begin = new DateTime($start_date);
    //             $end = new DateTime($end_date);
    //             $end->modify('+1 day');
    //             $interval = DateInterval::createFromDateString('1 day');
    //             $period   = new DatePeriod($begin, $interval, $end);
    //             $date_range = [];
    //             foreach ($period as $dt) {
    //                 $date_range[] = $dt->format(env('Date_Format'));
    //             }
    //             $emp_attendance_date_range = [];

    //             foreach ($employee as $key1 => $emp) {
    //                 $all_attendances_array = $emp->employeeAttendance->groupBy('attendance_date')->toArray();
    //                 $leaves = $emp->employeeLeave;
    //                 $shift = $emp->officeShift->toArray();
    //                 $holidays = $emp->company->companyHolidays;
    //                 $joining_date = Carbon::parse($emp->joining_date)->format(env('Date_Format'));
    //                 foreach ($date_range as $key2 => $dt_r) {
    //                     $emp_attendance_date_range[$key1*count($date_range)+$key2]['id'] = $emp->id;
    //                     $emp_attendance_date_range[$key1*count($date_range)+$key2]['employee_name'] = ($key2==0) ? '<strong>'.$emp->full_name.'</strong>' : $emp->full_name;
    //                     $emp_attendance_date_range[$key1*count($date_range)+$key2]['company'] = $emp->company->company_name;
    //                     $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_date'] = Carbon::parse($dt_r)->format(env('Date_Format'));

    //                     //attendance status
    //                     $day = strtolower(Carbon::parse($dt_r)->format('l')) . '_in';
    //                     if (strtotime($dt_r) < strtotime($joining_date))
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('Not Join');
    //                     }
    //                     elseif (empty($shift[$day]))
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('Off Day');
    //                     }
    //                     elseif (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = trans('file.present');
    //                     }
    //                     else
    //                     {
    //                         foreach ($leaves as $leave)
    //                         {
    //                             if ($leave->start_date <= $dt_r && $leave->end_date >= $dt_r)
    //                             {
    //                                 $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('On Leave');
    //                             }
    //                         }
    //                         foreach ($holidays as $holiday)
    //                         {
    //                             if ($holiday->start_date <= $dt_r && $holiday->end_date >= $dt_r)
    //                             {
    //                                 $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('On Holiday');
    //                             }
    //                         }
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = trans('Absent');
    //                     }
    //                     //attendance status

    //                     //clock in
    //                     if (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $first = current($all_attendances_array[$dt_r])['clock_in'];
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_in'] = $first;
    //                     }
    //                     else
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_in'] = '---';
    //                     }
    //                     //clock in

    //                     //clock out
    //                     if (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $last = end($all_attendances_array[$dt_r])['clock_out'];
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_out'] = $last;
    //                     }
    //                     else
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_out'] = '---';
    //                     }
    //                     //clock out

    //                     //time late
    //                     if (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $first = current($all_attendances_array[$dt_r])['time_late'];
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['time_late'] = $first;
    //                     } else
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['time_late'] = '---';
    //                     }
    //                     //time late

    //                     //early_leaving
    //                     if (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $last = end($all_attendances_array[$dt_r])['early_leaving'];
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['early_leaving'] = $last;
    //                     } else
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['early_leaving'] = '---';
    //                     }
    //                     //early_leaving

    //                     //overtime
    //                     if (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $total = 0;
    //                         foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
    //                         {
    //                             sscanf($all_attendance_item['overtime'], '%d:%d', $hour, $min);
    //                             $total += $hour * 60 + $min;
    //                         }
    //                         if ($h = floor($total / 60))
    //                         {
    //                             $total %= 60;
    //                         }
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['overtime'] = sprintf('%02d:%02d', $h, $total);
    //                     } else
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['overtime'] = '---';
    //                     }
    //                     //overtime

    //                     //total_work
    //                     if (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $total = 0;
    //                         foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
    //                         {
    //                             sscanf($all_attendance_item['total_work'], '%d:%d', $hour, $min);
    //                             $total += $hour * 60 + $min;
    //                         }
    //                         if ($h = floor($total / 60))
    //                         {
    //                             $total %= 60;
    //                         }
    //                         $sum_total = 0 + $total;
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_work'] = sprintf('%02d:%02d', $h, $total);
    //                     }
    //                     else
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_work'] = '---';
    //                     }
    //                     //total_work

    //                     //total_rest
    //                     if (array_key_exists($dt_r, $all_attendances_array))
    //                     {
    //                         $total = 0;
    //                         foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
    //                         {
    //                             //formatting in hour:min and separating them
    //                             sscanf($all_attendance_item['total_rest'], '%d:%d', $hour, $min);
    //                             //converting in minute
    //                             $total += $hour * 60 + $min;
    //                         }
    //                         // if minute is greater than hour then $h= hour
    //                         if ($h = floor($total / 60))
    //                         {
    //                             //$total = minute (after excluding hour)
    //                             $total %= 60;
    //                         }
    //                         //returning back to hour:minute format
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_rest'] = sprintf('%02d:%02d', $h, $total);
    //                     } else
    //                     {
    //                         $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_rest'] = '---';
    //                     }
    //                     //total_rest
    //                 }
    //             }
    //         }

    //         return datatables()->of($emp_attendance_date_range)
    //             ->setRowId(function ($row)
    //             {
    //                 return $row['id'];
    //             })
    //             ->addColumn('employee_name', function ($row)
    //             {
    //                 return $row['employee_name'];
    //             })
    //             ->addColumn('company', function ($row)
    //             {
    //                 return $row['company'];
    //             })
    //             ->addColumn('attendance_date', function ($row)
    //             {
    //                 return $row['attendance_date'];
    //             })
    //             ->addColumn('attendance_status', function ($row)
    //             {
    //                 return $row['attendance_status'];
    //             })
    //             ->addColumn('clock_in', function ($row)
    //             {
    //                 return $row['clock_in'];
    //             })
    //             ->addColumn('clock_out', function ($row)
    //             {
    //                 return $row['clock_out'];
    //             })
    //             ->addColumn('time_late', function ($row)
    //             {
    //                 return $row['time_late'];
    //             })
    //             ->addColumn('early_leaving', function ($row)
    //             {
    //                 return $row['early_leaving'];
    //             })
    //             ->addColumn('overtime', function ($row)
    //             {
    //                 return $row['overtime'];
    //             })
    //             ->addColumn('total_work', function ($row)
    //             {
    //                 return $row['total_work'];
    //             })
    //             ->addColumn('total_rest', function ($row)
    //             {
    //                 return $row['total_rest'];
    //             })
    //             ->rawColumns(['action','employee_name'])
    //             ->make(true);
    //     }

    //     return view('timesheet.dateWiseAttendance.index', compact('companies'));
	// }

	public function dateWiseAttendance(Request $request)
	{
		$logged_user = auth()->user();

        $companies = Company::all('id', 'company_name');
        $start_date = Carbon::parse($request->filter_start_date)->format('Y-m-d') ?? '';
        $end_date = Carbon::parse($request->filter_end_date)->format('Y-m-d') ?? '';
        
        // Define selected_date for the view
        $selected_date = now()->format('F-Y');
        // $start_date = Carbon::parse('2023-02-18')->format('Y-m-d') ?? '';
        // $end_date = Carbon::parse('2023-02-20')->format('Y-m-d') ?? '';

        // Test START
        // return $this->test($request, $companies, $start_date, $end_date);
        // Test END


        if (request()->ajax())
        {
            if (!$request->company_id && !$request->department_id && !$request->employee_id)
            {
                $emp_attendance_date_range = [];
            }
            else
            {
                $employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($start_date, $end_date)
                {
                    $query->whereBetween('attendance_date', [$start_date, $end_date]);
                },
                    'employeeLeave',
                    'company:id,company_name',
                    'company.companyHolidays'
                ])
                ->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id', 'joining_date')
                ->where('is_active', '=', 1);

                if ($request->employee_id) {
                    $employee = $employee->where('id', '=', $request->employee_id)->get();
                }
                elseif ($request->department_id) {
                    $employee = $employee->where('department_id', '=', $request->department_id)->get();
                }
                elseif ($request->company_id) {
                    $employee = $employee->where('company_id', '=', $request->company_id)->get();
                }

                $begin = new DateTime($start_date);
                $end = new DateTime($end_date);
                $end->modify('+1 day');
                $interval = DateInterval::createFromDateString('1 day');
                $period   = new DatePeriod($begin, $interval, $end);
                $date_range = [];
                foreach ($period as $dt) {
                    $date_range[] = $dt->format(env('Date_Format'));
                }
                $emp_attendance_date_range = [];

                foreach ($employee as $key1 => $emp) {
                    $all_attendances_array = $emp->employeeAttendance->groupBy('attendance_date')->toArray();
                    $leaves = $emp->employeeLeave;
                    $shift = $emp->officeShift->toArray();
                    $holidays = $emp->company->companyHolidays;
                    $joining_date = Carbon::parse($emp->joining_date)->format(env('Date_Format'));
                    foreach ($date_range as $key2 => $dt_r) {
                        $emp_attendance_date_range[$key1*count($date_range)+$key2]['id'] = $emp->id;
                        $emp_attendance_date_range[$key1*count($date_range)+$key2]['employee_name'] = ($key2==0) ? '<strong>'.$emp->full_name.'</strong>' : $emp->full_name;
                        $emp_attendance_date_range[$key1*count($date_range)+$key2]['company'] = $emp->company->company_name;
                        $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_date'] = Carbon::parse($dt_r)->format(env('Date_Format'));

                        //attendance status
                        $day = strtolower(Carbon::parse($dt_r)->format('l')) . '_in';
                        if (strtotime($dt_r) < strtotime($joining_date))
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('Not Join');
                        }
                        elseif (empty($shift[$day]))
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('Off Day');
                        }
                        elseif (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = trans('file.present');
                        }
                        else
                        {
                            foreach ($leaves as $leave)
                            {
                                if ($leave->start_date <= $dt_r && $leave->end_date >= $dt_r)
                                {
                                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('On Leave');
                                }
                            }
                            foreach ($holidays as $holiday)
                            {
                                if ($holiday->start_date <= $dt_r && $holiday->end_date >= $dt_r)
                                {
                                    $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = __('On Holiday');
                                }
                            }
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['attendance_status'] = trans('Absent');
                        }
                        //attendance status

                        //clock in
                        if (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $first = current($all_attendances_array[$dt_r])['clock_in'];
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_in'] = $first;
                        }
                        else
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_in'] = '---';
                        }
                        //clock in

                        //clock out
                        if (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $last = end($all_attendances_array[$dt_r])['clock_out'];
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_out'] = $last;
                        }
                        else
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['clock_out'] = '---';
                        }
                        //clock out

                        //time late
                        if (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $first = current($all_attendances_array[$dt_r])['time_late'];
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['time_late'] = $first;
                        } else
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['time_late'] = '---';
                        }
                        //time late

                        //early_leaving
                        if (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $last = end($all_attendances_array[$dt_r])['early_leaving'];
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['early_leaving'] = $last;
                        } else
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['early_leaving'] = '---';
                        }
                        //early_leaving

                        //overtime
                        if (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $total = 0;
                            foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
                            {
                                sscanf($all_attendance_item['overtime'], '%d:%d', $hour, $min);
                                $total += $hour * 60 + $min;
                            }
                            if ($h = floor($total / 60))
                            {
                                $total %= 60;
                            }
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['overtime'] = sprintf('%02d:%02d', $h, $total);
                        } else
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['overtime'] = '---';
                        }
                        //overtime

                        //total_work
                        if (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $total = 0;
                            foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
                            {
                                sscanf($all_attendance_item['total_work'], '%d:%d', $hour, $min);
                                $total += $hour * 60 + $min;
                            }
                            if ($h = floor($total / 60))
                            {
                                $total %= 60;
                            }
                            $sum_total = 0 + $total;
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_work'] = sprintf('%02d:%02d', $h, $total);
                        }
                        else
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_work'] = '---';
                        }
                        //total_work

                        //total_rest
                        if (array_key_exists($dt_r, $all_attendances_array))
                        {
                            $total = 0;
                            foreach ($all_attendances_array[$dt_r] as $all_attendance_item)
                            {
                                //formatting in hour:min and separating them
                                sscanf($all_attendance_item['total_rest'], '%d:%d', $hour, $min);
                                //converting in minute
                                $total += $hour * 60 + $min;
                            }
                            // if minute is greater than hour then $h= hour
                            if ($h = floor($total / 60))
                            {
                                //$total = minute (after excluding hour)
                                $total %= 60;
                            }
                            //returning back to hour:minute format
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_rest'] = sprintf('%02d:%02d', $h, $total);
                        } else
                        {
                            $emp_attendance_date_range[$key1*count($date_range)+$key2]['total_rest'] = '---';
                        }
                        //total_rest
                    }
                }
            }

            return datatables()->of($emp_attendance_date_range)
                ->setRowId(function ($row)
                {
                    return $row['id'];
                })
                ->addColumn('employee_name', function ($row)
                {
                    return $row['employee_name'];
                })
                ->addColumn('company', function ($row)
                {
                    return $row['company'];
                })
                ->addColumn('attendance_date', function ($row)
                {
                    return $row['attendance_date'];
                })
                ->addColumn('attendance_status', function ($row)
                {
                    return $row['attendance_status'];
                })
                ->addColumn('clock_in', function ($row)
                {
                    return $row['clock_in'];
                })
                ->addColumn('clock_out', function ($row)
                {
                    return $row['clock_out'];
                })
                ->addColumn('time_late', function ($row)
                {
                    return $row['time_late'];
                })
                ->addColumn('early_leaving', function ($row)
                {
                    return $row['early_leaving'];
                })
                ->addColumn('overtime', function ($row)
                {
                    return $row['overtime'];
                })
                ->addColumn('total_work', function ($row)
                {
                    return $row['total_work'];
                })
                ->addColumn('total_rest', function ($row)
                {
                    return $row['total_rest'];
                })
                ->rawColumns(['action','employee_name'])
                ->make(true);
        }

        return view('timesheet.dateWiseAttendance.index', compact('companies', 'selected_date'));

	}


	public function monthlyAttendance(Request $request)
	{
		$logged_user = auth()->user();
		$companies = Company::all('id', 'company_name');

		$month_year = $request->filter_month_year;
		
		// Define selected_date for the view
		$selected_date = $month_year ?? now()->format('F-Y');


		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date = date('Y-m-d', strtotime('last day of ' . $month_year));

		$begin = new DateTime($first_date);
		$end = new DateTime($last_date);

		$end->modify('+1 day');

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);


		foreach ($period as $dt)
		{
			$this->date_range[] = $dt->format("d D");
			$this->date_attendance[] = $dt->format(env('Date_Format'));
		}


		// if ($logged_user->can('view-attendance'))
		// {
			if (request()->ajax())
			{
				if(!($logged_user->can('monthly-attendances'))) //Correction
				{
					$employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($first_date, $last_date)
					{
						$query->whereBetween('attendance_date', [$first_date, $last_date]);
					},
						'employeeLeave',
						'company:id,company_name',
						'company.companyHolidays'
					])
					->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id')
                    ->where('is_active',1)
                    ->where('exit_date',NULL)
                    ->whereId($logged_user->id)
                    ->get();
				}
				else
				{
					//Previous
					if (!empty($request->filter_company && $request->filter_employee))
					{

						$employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($first_date, $last_date)
						{
							$query->whereBetween('attendance_date', [$first_date, $last_date]);
						},
							'employeeLeave',
							'company:id,company_name',
							'company.companyHolidays'
						])
							->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id')
							->whereId($request->filter_employee)->get();

					} elseif (!empty($request->filter_company))
					{
						$employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($first_date, $last_date)
						{
							$query->whereBetween('attendance_date', [$first_date, $last_date]);
						},
							'employeeLeave',
							'company:id,company_name',
							'company.companyHolidays'
						])
							->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id')
							->where('company_id', $request->filter_company)->where('is_active',1)
                            ->where('exit_date',NULL)->get();
					}
					else
					{
						$employee = Employee::with(['officeShift', 'employeeAttendance' => function ($query) use ($first_date, $last_date)
						{
							$query->whereBetween('attendance_date', [$first_date, $last_date]);
						},
							'employeeLeave',
							'company:id,company_name',
							'company.companyHolidays'
						])
							->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id')
                            ->where('is_active',1)
                            ->where('exit_date',NULL)
							->get();
					}
				}

				return datatables()->of($employee)
					->setRowId(function ($row)
					{
						$this->work_days = 0;

						return $row->id;
					})
					->addColumn('employee_name', function ($row)
					{
						$name = $row->full_name;
						$company_name = $row->company->company_name;

						return $name . '(' . $company_name . ')';

					})
					->addColumn('day1', function ($row)
					{
						return $this->checkAttendanceStatus($row, 0);
					})
					->addColumn('day2', function ($row)
					{
						return $this->checkAttendanceStatus($row, 1);
					})
					->addColumn('day3', function ($row)
					{
						return $this->checkAttendanceStatus($row, 2);
					})
					->addColumn('day4', function ($row)
					{
						return $this->checkAttendanceStatus($row, 3);
					})
					->addColumn('day5', function ($row)
					{
						return $this->checkAttendanceStatus($row, 4);
					})
					->addColumn('day6', function ($row)
					{
						return $this->checkAttendanceStatus($row, 5);
					})
					->addColumn('day7', function ($row)
					{
						return $this->checkAttendanceStatus($row, 6);
					})
					->addColumn('day8', function ($row)
					{
						return $this->checkAttendanceStatus($row, 7);
					})
					->addColumn('day9', function ($row)
					{
						return $this->checkAttendanceStatus($row, 8);
					})
					->addColumn('day10', function ($row)
					{
						return $this->checkAttendanceStatus($row, 9);
					})
					->addColumn('day11', function ($row)
					{
						return $this->checkAttendanceStatus($row, 10);
					})
					->addColumn('day12', function ($row)
					{
						return $this->checkAttendanceStatus($row, 11);
					})
					->addColumn('day13', function ($row)
					{
						return $this->checkAttendanceStatus($row, 12);
					})
					->addColumn('day14', function ($row)
					{
						return $this->checkAttendanceStatus($row, 13);
					})
					->addColumn('day15', function ($row)
					{
						return $this->checkAttendanceStatus($row, 14);
					})
					->addColumn('day16', function ($row)
					{
						return $this->checkAttendanceStatus($row, 15);
					})
					->addColumn('day17', function ($row)
					{
						return $this->checkAttendanceStatus($row, 16);
					})
					->addColumn('day18', function ($row)
					{
						return $this->checkAttendanceStatus($row, 17);
					})
					->addColumn('day19', function ($row)
					{
						return $this->checkAttendanceStatus($row, 18);
					})
					->addColumn('day20', function ($row)
					{
						return $this->checkAttendanceStatus($row, 19);
					})
					->addColumn('day21', function ($row)
					{
						return $this->checkAttendanceStatus($row, 20);
					})
					->addColumn('day22', function ($row)
					{
						return $this->checkAttendanceStatus($row, 21);
					})
					->addColumn('day23', function ($row)
					{
						return $this->checkAttendanceStatus($row, 22);
					})
					->addColumn('day24', function ($row)
					{
						return $this->checkAttendanceStatus($row, 23);
					})
					->addColumn('day25', function ($row)
					{
						return $this->checkAttendanceStatus($row, 24);
					})
					->addColumn('day26', function ($row)
					{
						return $this->checkAttendanceStatus($row, 25);
					})
					->addColumn('day27', function ($row)
					{
						return $this->checkAttendanceStatus($row, 26);
					})
					->addColumn('day28', function ($row)
					{
						return $this->checkAttendanceStatus($row, 27);
					})
					->addColumn('day29', function ($row)
					{
						return $this->checkAttendanceStatus($row, 28);
					})
					->addColumn('day30', function ($row)
					{
						return $this->checkAttendanceStatus($row, 29);
					})
					->addColumn('day31', function ($row)
					{
						return $this->checkAttendanceStatus($row, 30);
					})
					->addColumn('worked_days', function ($row)
					{
						return $this->work_days;
					})
					->addColumn('total_worked_hours', function ($row)
					{
						return $this->totalWorkedHours($row);
					})
					// ->addColumn('total_worked_hours', function ($row) use ($month_year)
					// {
					// 	if ($month_year) {
					// 		return $this->MonthlyTotalWorked($month_year,$row->id);
					// 	}
					// 	else{
					// 		return $this->totalWorkedHours($row);
					// 	}
					// })
					->with([
						'date_range' => $this->date_range,
					])
					->make(true);
			}

			return view('timesheet.monthlyAttendance.index', compact('companies', 'selected_date'));
		// }
		// return response()->json(['success' => __('You are not authorized')]);
	}


	public function checkAttendanceStatus($emp, $index)
	{

		if (count($this->date_attendance) <= $index)
		{
			return '';
		} else
		{
			$present = $emp->employeeAttendance->where('attendance_date', $this->date_attendance[$index]);

			$leave = $emp->employeeLeave->where('start_date', '<=', $this->date_attendance[$index])
				->where('end_date', '>=', $this->date_attendance[$index]);

			$holiday = $emp->company->companyHolidays->where('start_date', '<=', $this->date_attendance[$index])
				->where('end_date', '>=', $this->date_attendance[$index]);

			$day = strtolower(Carbon::parse($this->date_attendance[$index])->format('l')) . '_in';

			if ($present->isNotEmpty())
			{
				$this->work_days++;

				return 'P';
			} elseif (!$emp->officeShift->$day)
			{
				return 'O';
			} elseif ($leave->isNotEmpty())
			{
				return 'L';
			} elseif ($holiday->isNotEmpty())
			{
				return 'H';
			} else
			{
				return 'A';
			}
		}
	}

	public function updateAttendance(Request $request)
	{
		$logged_user = auth()->user();
		$companies = company::select('id', 'company_name')->get();
		
		// Define selected_date for the view
		$selected_date = now()->format('F-Y');
		
		if ($logged_user->can('edit-attendance'))
		{
			if (request()->ajax())
			{

				$employee_attendance = Attendance::where('employee_id', $request->employee_id)
                    ->whereDate('attendance_date','>=', Carbon::parse($request->attendance_date1)->format('Y-m-d'))
                    ->whereDate('attendance_date','<=', Carbon::parse($request->attendance_date2)->format('Y-m-d'))
					->get();


				return datatables()->of($employee_attendance)
					->setRowId(function ($row)
					{
						return $row->id;
					})
                    ->addColumn('date', function ($row)
					{
						return $row->attendance_date;
					})
					->addColumn('clock_in', function ($row)
					{
						return $row->clock_in;
					})
					->addColumn('clock_out', function ($row)
					{
						return $row->clock_out;
					})
					->addColumn('action', function ($row)
					{
						if (auth()->user()->can('user-edit'))
						{
							$button = '<button type="button" name="edit" id="' . $row->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
							$button .= '&nbsp;&nbsp;&nbsp;';
							$button .= '<button type="button" name="delete" id="' . $row->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';

							return $button;
						} else
						{
							return '';
						}
					})
					->rawColumns(['action'])
					->make(true);
			}

			return view('timesheet.updateAttendance.index', compact('companies', 'selected_date'));
		}
		return response()->json(['success' => __('You are not authorized')]);
	}

	public function updateAttendanceGet($id)
	{
		$attendance = Attendance::select('id', 'clock_in', 'clock_out', 'attendance_date')
			->findOrFail($id);
        $attendance->clock_in = (new DateTime($attendance->clock_in))->format('h:iA');
        $attendance->clock_out = (new DateTime($attendance->clock_out))->format('h:iA');
		return response()->json(['data' => $attendance]);
	}

	public function updateAttendanceStore(Request $request)
	{
		try {
			$data = $this->attendanceHandler($request);
			
			// Check if attendanceHandler returned an error response
			if (is_array($data) && isset($data['errors'])) {
				return response()->json($data);
			}
			
			// Check if it returned an exception
			if ($data instanceof Exception) {
				return response()->json(['errors' => [$data->getMessage()]]);
			}
			
			// Ensure we have all required fields
			if (empty($data) || !is_array($data) || !isset($data['employee_id'])) {
				return response()->json(['errors' => ['Invalid data format from attendance handler']]);
			}
			
			// Add missing required fields
			$data['clock_in_ip'] = $request->ip();
			$data['clock_out_ip'] = $request->ip();
			$data['attendance_status'] = 'present';
			
			Attendance::create($data);
			return response()->json(['success' => __('Attendance has been successfully added')]);
		} catch (\Exception $e) {
			return response()->json(['errors' => ['Error: ' . $e->getMessage()]]);
		}
	}

	public function attendanceHandler($request)
	{
		$validator = Validator::make($request->only('attendance_date', 'clock_in', 'clock_out', 'employee_id'),
			[
				'attendance_date' => 'required|date',
				'clock_in' => 'required',
				'clock_out' => 'required',
				'employee_id' => 'required|exists:employees,id'
			]);

		if ($validator->fails())
		{
			return ['errors' => $validator->errors()->all()];
		}

		$employee_id = $request->employee_id;
		$attendance_date = $request->attendance_date;
		try
		{
			$clock_in = new DateTime($request->clock_in);
			$clock_out = new DateTime($request->clock_out);
		} catch (Exception $e)
		{
			return ['errors' => ['Invalid time format: ' . $e->getMessage()]];
		}

        try {
            $employee = Employee::with('officeShift')->findOrFail($employee_id);
        } catch (Exception $e) {
            return ['errors' => ['Employee not found']];
        }
        
        if (!$employee->officeShift) {
            return ['errors' => ['Employee does not have an assigned shift']];
        }

		$attendance_date_day = Carbon::parse($attendance_date);
		$current_day_in = strtolower($attendance_date_day->format('l')) . '_in';
		$current_day_out = strtolower($attendance_date_day->format('l')) . '_out';
		
        try
		{
			$shift_in_time = $employee->officeShift->$current_day_in;
            $shift_out_time = $employee->officeShift->$current_day_out;
            
            if (!$shift_in_time || !$shift_out_time) {
                return ['errors' => ['No shift timing configured for ' . $attendance_date_day->format('l')]];
            }
            
			$shift_in = new DateTime($shift_in_time);
            $shift_out = new DateTime($shift_out_time);
		} catch (Exception $e)
		{
			return ['errors' => ['Invalid shift timing: ' . $e->getMessage()]];
		}

        $employee_attendance_last = Attendance::where('attendance_date', $attendance_date_day->format('Y-m-d'))
                ->where('employee_id', $employee_id)->orderBy('id', 'desc')->first() ?? null;


        $time_late = '00:00';
        $early_leaving = '00:00';
        $overtime = '00:00';
        $total_work = '00:00';
        $total_rest = '00:00';
        $data = [];
        //if employee attendance record was not found
        if (!$employee_attendance_last)
        {
            // if employee is late
            if ($clock_in > $shift_in)
            {
                $time_late = $this->calculateLateTimeWithGracePeriod($shift_in, $clock_in);
            } // if employee is early or on time
            else
            {
                if(env('ENABLE_EARLY_CLOCKIN') == NULL) {
                    $clock_in = $shift_in;
                }
            }
            if ($clock_out > $shift_in || env('ENABLE_EARLY_CLOCKIN')!=NULL) {
                // if employee is early leaving
                if ($clock_out < $shift_out) {
                    $timeDifference = $shift_out->diff($clock_out)->format('%H:%I');
                    $early_leaving = $timeDifference;
                }

                // calculating total work
                $total_work = $clock_in->diff($clock_out)->format('%H:%I');
                $total_work_dt = new DateTime($total_work);
                // Overtime calculation with late deduction and 15-minute grace period
                $overtime = $this->calculateOvertimeWithLateDeduction($shift_out, $clock_out, $shift_in, $clock_in);
                $data['employee_id'] = $employee_id;
                $data['attendance_date'] = $attendance_date;
                $data['clock_in'] = $clock_in->format('H:i');
                $data['clock_out'] = $clock_out->format('H:i');
                $data['clock_in_out'] = 0;
                $data['time_late'] = $time_late;
                $data['early_leaving'] = $early_leaving;
                $data['overtime'] = $overtime;
                $data['total_work'] = $total_work;
            }
        }
        // if there is a record of employee attendance
        else {
            // last clock out (needed for calculation rest time)
            $employee_last_clock_out = new DateTime($employee_attendance_last->clock_out);
            $total_rest = $employee_last_clock_out->diff($clock_in)->format('%H:%I');

            // if employee is early leaving
            if ($clock_out < $shift_out) {
                $timeDifference = $shift_out->diff($clock_out)->format('%H:%I');
                $early_leaving = $timeDifference;
            }
            $prev_work = new DateTime($employee_attendance_last->total_work);
            $total_work_dt = $prev_work->add($clock_in->diff($clock_out));
            $total_work = $total_work_dt->format('H:i');
            // Overtime calculation with late deduction and 15-minute grace period
            $overtime = $this->calculateOvertimeWithLateDeduction($shift_out, $clock_out, $shift_in, $clock_in);
            Attendance::whereId($employee_attendance_last->id)->update(['total_work'=> '00:00', 'overtime'=> '00:00']);
            $data['employee_id'] = $employee_id;
            $data['attendance_date'] = $attendance_date;
            $data['clock_in'] = $clock_in->format('H:i');
            $data['clock_out'] = $clock_out->format('H:i');
            $data['clock_in_out'] = 0;
            $data['time_late'] = $time_late;
            $data['early_leaving'] = $early_leaving;
            $data['overtime'] = $overtime;
            $data['total_work'] = $total_work;
            $data['total_rest'] = $total_rest;
        }
		return $data;
	}

	public function updateAttendanceUpdate(Request $request)
	{

		$validator = Validator::make($request->only('attendance_date', 'clock_in', 'clock_out'),
			[
				'attendance_date' => 'required|date',
				'clock_in' => 'required',
				'clock_out' => 'required'
			]);


		if ($validator->fails())
		{
			return response()->json(['errors' => $validator->errors()->all()]);
		}

		try
		{
			$clock_in = new DateTime($request->clock_in);
			$clock_out = new DateTime($request->clock_out);
		} catch (Exception $e)
		{
			return $e;
		}

        if ($clock_in > $clock_out) {
            return response()->json(['errors' => [__('Clock in cannot be greater than clock out')]]);
        }

        $id = $request->hidden_id;
        $employee_id = $request->employee_id;
		$attendance_date = $request->attendance_date;
        $employee = Employee::with('officeShift')->findOrFail($employee_id);
		$attendance_date_day = Carbon::parse($attendance_date);
		$current_day_in = strtolower($attendance_date_day->format('l')) . '_in';
		$current_day_out = strtolower($attendance_date_day->format('l')) . '_out';

        try
		{
			$shift_in = new DateTime($employee->officeShift->$current_day_in);
            $shift_out = new DateTime($employee->officeShift->$current_day_out);
		} catch (Exception $e)
		{
			return $e;
		}

        $employee_attendance = Attendance::where('employee_id', $employee_id)
        ->whereDate('attendance_date', $attendance_date_day->format('Y-m-d'))
        ->get()->toArray();
        $no_emp_att = count($employee_attendance);


        $time_late = '00:00';
        $early_leaving = '00:00';
        $overtime = '00:00';
        $total_work = '00:00';
        $total_rest = '00:00';
        $data = [];

        for ($i=0; $i < $no_emp_att; $i++) {
            if ($employee_attendance[$i]['id'] == $id) {
				// if employee is late
				if ($clock_in > $shift_in)
				{
					if ($i == 0) {
						$time_late = $this->calculateLateTimeWithGracePeriod($shift_in, $clock_in);
					}
				} // if employee is early or on time
				else
				{
					if(env('ENABLE_EARLY_CLOCKIN') == NULL) {
						$clock_in = $shift_in;
					}
				}
				if ($clock_out > $shift_in || env('ENABLE_EARLY_CLOCKIN')!=NULL) {
					// if employee is early leaving
					if ($clock_out < $shift_out) {
						$timeDifference = $shift_out->diff($clock_out)->format('%H:%I');
						$early_leaving = $timeDifference;
					}

					// calculating total work
					$total_work = $clock_in->diff($clock_out)->format('%H:%I');
					$total_work_dt = new DateTime($total_work);
					// Overtime calculation
					$duty_time = new DateTime($shift_in->diff($shift_out)->format('%H:%I'));

					$data['employee_id'] = $employee_id;
					$data['attendance_date'] = $attendance_date;
					$data['clock_in'] = $clock_in->format('H:i');
					$data['clock_out'] = $clock_out->format('H:i');
					$data['clock_in_out'] = 0;
					$data['time_late'] = $time_late;
					$data['early_leaving'] = $early_leaving;

					if ($no_emp_att > 1) {
						if ($i != $no_emp_att-1) {
							$next_clock_in = (new DateTime($employee_attendance[$i+1]['clock_in']));
							if ($clock_out > $next_clock_in) {
								return response()->json(['errors' => [__('Clock out cannot be greater than next clock in')]]);
							}
							else {
								$total_rest = $clock_out->diff($next_clock_in)->format('%H:%I');
								Attendance::find($employee_attendance[$i+1]['id'])->update(['total_rest'=> $total_rest]);
							}
						}
						if ($i != 0) {
							$prev_clock_out = (new DateTime($employee_attendance[$i-1]['clock_out']));
							if ($clock_in < $prev_clock_out) {
								return response()->json(['errors' => [__('Clock in cannot be lower than previous clock out')]]);
							}
							else {
								$total_rest = $prev_clock_out->diff($clock_in)->format('%H:%I');
								Attendance::find($employee_attendance[$i]['id'])->update(['total_rest'=> $total_rest]);
							}
						}

						$before_change_clock_in = new DateTime($employee_attendance[$i]['clock_in']);
						$before_change_clock_out = new DateTime($employee_attendance[$i]['clock_out']);
						$before_change_work = new DateTime($before_change_clock_in->diff($before_change_clock_out)->format('%H:%I'));
						$before_change_total_work = new DateTime($employee_attendance[$no_emp_att-1]['total_work']);
						$total_work_dt = $total_work_dt->add($before_change_work->diff($before_change_total_work));
						$total_work = $total_work_dt->format('H:i');

						if ($total_work_dt > $duty_time) {
							$overtime = $total_work_dt->diff($duty_time)->format('%H:%I');
						}
						Attendance::find($employee_attendance[$no_emp_att-1]['id'])->update(['total_work'=> $total_work, 'overtime'=> $overtime]);
					}
					else {
						if ($total_work_dt > $duty_time) {
							$overtime = $total_work_dt->diff($duty_time)->format('%H:%I');
						}
						$data['overtime'] = $overtime;
						$data['total_work'] = $total_work;
					}

					Attendance::find($employee_attendance[$i]['id'])->update($data);
					return response()->json(['success' => __('Data is successfully updated')]);
				}
				else
				{
					return response()->json(['errors' => ['Clock out can not be lower than Shift in']]);
				}
                break;
            }
        }
	}

	public function updateAttendanceDelete($id)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('delete-attendance'))
		{
            $deleted_att_info = Attendance::find($id);

            $clock_in = new DateTime($deleted_att_info->clock_in);
            $clock_out = new DateTime($deleted_att_info->clock_out);

            $employee_id = $deleted_att_info->employee_id;
            $attendance_date = $deleted_att_info->attendance_date;
            $employee = Employee::with('officeShift')->findOrFail($employee_id);
            $attendance_date_day = Carbon::parse($attendance_date);
            $current_day_in = strtolower($attendance_date_day->format('l')) . '_in';
            $current_day_out = strtolower($attendance_date_day->format('l')) . '_out';

            try
            {
                $shift_in = new DateTime($employee->officeShift->$current_day_in);
                $shift_out = new DateTime($employee->officeShift->$current_day_out);
            } catch (Exception $e)
            {
                return $e;
            }

            $employee_attendance = Attendance::where('employee_id', $employee_id)
            ->whereDate('attendance_date', $attendance_date_day->format('Y-m-d'))
            ->get()->toArray();
            $no_emp_att = count($employee_attendance);

            for ($i=0; $i < $no_emp_att; $i++) {
                if ($employee_attendance[$i]['id'] == $id) {
                    if ($no_emp_att > 1) {
                        if ($i == 0) {
							$time_late = '00:00';
							$next_clock_in = (new DateTime($employee_attendance[$i+1]['clock_in']));
							// if employee is late
							if ($next_clock_in > $shift_in) {
								$time_late = $this->calculateLateTimeWithGracePeriod($shift_in, $next_clock_in);
							}
                            Attendance::find($employee_attendance[$i+1]['id'])->update(['time_late'=> $time_late, 'total_rest'=> '00:00']);
                        }
                        elseif ($i != $no_emp_att-1) {
                            $prev_clock_out = (new DateTime($employee_attendance[$i-1]['clock_out']));
                            $next_clock_in = (new DateTime($employee_attendance[$i+1]['clock_in']));
                            $total_rest = $prev_clock_out->diff($next_clock_in)->format('%H:%I');
                            Attendance::find($employee_attendance[$i+1]['id'])->update(['total_rest'=> $total_rest]);
                        }
                        // Overtime calculation
                        $duty_time = new DateTime($shift_in->diff($shift_out)->format('%H:%I'));
                        $before_delete_work = new DateTime($clock_in->diff($clock_out)->format('%H:%I'));
                        $before_delete_total_work = new DateTime($employee_attendance[$no_emp_att-1]['total_work']);
                        $total_work = $before_delete_work->diff($before_delete_total_work)->format('%H:%I');
                        $total_work_dt = new DateTime($total_work);
                        $overtime = '00:00';
                        if ($total_work_dt > $duty_time) {
                            $overtime = $total_work_dt->diff($duty_time)->format('%H:%I');
                        }

                        if ($i == $no_emp_att-1) {
                            Attendance::find($employee_attendance[$no_emp_att-2]['id'])->update(['total_work'=> $total_work, 'overtime'=> $overtime]);
                        }
                        else {
                            Attendance::find($employee_attendance[$no_emp_att-1]['id'])->update(['total_work'=> $total_work, 'overtime'=> $overtime]);
                        }
                    }
                    Attendance::whereId($id)->delete();
                    return response()->json(['success' => __('Data is successfully deleted')]);
                    break;
                }
            }
		}
		return response()->json(['error' => __('You are not authorized')]);
	}


	public function import()
	{
		$logged_user = auth()->user();
		if ($logged_user->can('delete-attendance'))
		{
			return view('timesheet.attendance.import');
		}
		return abort(404,__('You are not authorized'));
	}

    public function importDeviceCsv()
	{
        if (!env('USER_VERIFIED'))
		{
            $this->setErrorMessage('This feature is disabled for demo!');
            return redirect()->back();
		}
		try
		{
			Excel::queueImport(new AttendancesImportDevice(), request()->file('file'));
		} catch (ValidationException $e)
		{
			$failures = $e->failures();

            $error_msg = '';
            foreach ($failures as $failure) {
                $error_msg.= '<h4>Row No -'.$failure->row().'</h4>';
                foreach ($failure->errors() as $error) {
                    $error_msg.= '<li>'.$error.'</li>';
                }
            }
            $this->setErrorMessage($error_msg);
            return back();
		}
		$this->setSuccessMessage(__('Imported Successfully'));
		return back();
	}

	public function importPost()
	{
        if (!env('USER_VERIFIED'))
		{
            $this->setErrorMessage('This feature is disabled for demo!');
            return redirect()->back();
		}

		$file = request()->file('file');
		$fileExtension = $file->getClientOriginalExtension();

		try
		{
			// Use enhanced CSV import for CSV files
			if (in_array(strtolower($fileExtension), ['csv'])) {
				$import = new AttendancesCsvImport();
				Excel::import($import, $file);
				
				$summary = $import->getImportSummary();
				
				$message = "Import completed! ";
				$message .= "✅ {$summary['success_count']} records imported successfully. ";
				
				if ($summary['skipped_count'] > 0) {
					$message .= "⚠️ {$summary['skipped_count']} records skipped (duplicates). ";
				}
				
				if ($summary['error_count'] > 0) {
					$message .= "❌ {$summary['error_count']} records failed. ";
					if (!empty($summary['errors'])) {
						$message .= "<br><br><strong>Errors:</strong><br>";
						foreach (array_slice($summary['errors'], 0, 10) as $error) {
							$message .= "• " . $error . "<br>";
						}
						if (count($summary['errors']) > 10) {
							$message .= "• ... and " . (count($summary['errors']) - 10) . " more errors";
						}
					}
				}
				
				// Add debug info when there are issues or no successful imports
				if ($summary['success_count'] == 0 || $summary['error_count'] > 0) {
					if (!empty($summary['debug_info'])) {
						$message .= "<br><br><strong>Debug Information:</strong><br>";
						foreach (array_slice($summary['debug_info'], 0, 20) as $debug) {
							$message .= "• " . htmlspecialchars($debug) . "<br>";
						}
						if (count($summary['debug_info']) > 20) {
							$message .= "• ... and " . (count($summary['debug_info']) - 20) . " more debug entries";
						}
					}
				}
				
				if ($summary['error_count'] > 0) {
					$this->setErrorMessage($message);
				} else {
					$this->setSuccessMessage($message);
				}
			} else {
				// Use original import for Excel files
				Excel::queueImport(new AttendancesImport(), $file);
				$this->setSuccessMessage(__('Excel file imported successfully! Processing in background.'));
			}
		} catch (ValidationException $e)
		{
			$failures = $e->failures();

            $error_msg = '<h5>Validation Errors:</h5><ul>';
            foreach ($failures as $failure) {
                $error_msg.= '<li><strong>Row ' . $failure->row() . ':</strong>';
                foreach ($failure->errors() as $error) {
                    $error_msg.= ' ' . $error;
                }
                $error_msg.= '</li>';
            }
            $error_msg .= '</ul>';
            $this->setErrorMessage($error_msg);
            return back();
		} catch (\Exception $e) {
			$this->setErrorMessage('Import failed: ' . $e->getMessage());
			return back();
		}
		
		return back();
	}


	protected function MonthlyTotalWorked($month_year,$employeeId)
	{
		$year = date('Y',strtotime($month_year));
		$month = date('m',strtotime($month_year));

		$total = 0;

		$att = Employee::with(['employeeAttendance' => function ($query) use ($year,$month){
				$query->whereYear('attendance_date',$year)->whereMonth('attendance_date',$month);
			}])
			->select('id', 'company_id', 'first_name', 'last_name', 'office_shift_id')
			->whereId($employeeId)
			->get();

		//$count = count($att[0]->employeeAttendance);
		// return $att[0]->employeeAttendance[0]->total_work;

		foreach ($att[0]->employeeAttendance as $key => $a)
		{
			// return $att[0]->employeeAttendance[1]->total_work;
			// return $a->total_work;
			sscanf($a->total_work, '%d:%d', $hour, $min);
			$total += $hour * 60 + $min;
		}

		if ($h = floor($total / 60))
		{
			$total %= 60;
		}
		$sum_total = sprintf('%02d:%02d', $h, $total);

		return $sum_total;
	}

	/**
	 * Process biometric attendance with automatic shift detection and rule calculations
	 */
	public function processBiometricAttendance(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'employee_id' => 'required|exists:employees,id',
			'punch_in_time' => 'required|date_format:Y-m-d H:i:s',
			'punch_out_time' => 'required|date_format:Y-m-d H:i:s',
			'attendance_date' => 'required|date'
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()], 400);
		}

		$result = AttendanceProcessor::processAttendance(
			$request->employee_id,
			$request->punch_in_time,
			$request->punch_out_time,
			$request->attendance_date
		);

		if (isset($result['error'])) {
			return response()->json($result, 400);
		}

		return response()->json($result);
	}

	/**
	 * Generate enhanced daily attendance report with all calculations
	 */
	public function getDailyAttendanceReport(Request $request)
	{
		$date = $request->input('date', now()->format('Y-m-d'));
		$companyId = $request->input('company_id');

		$report = AttendanceProcessor::generateDailyReport($date, $companyId);

		if ($request->wants('json')) {
			return response()->json([
				'success' => true,
				'date' => $date,
				'report' => $report
			]);
		}

		return view('report.daily_attendance_enhanced', [
			'report' => $report,
			'date' => $date
		]);
	}

	/**
	 * Generate Extra OT Report for hours beyond 2 per day
	 */
	public function getExtraOTReport(Request $request)
	{
		$date = $request->input('date', now()->format('Y-m-d'));
		$companyId = $request->input('company_id');

		$extraOTReport = AttendanceProcessor::getExtraOTReport($date, $companyId);

		if ($request->wants('json')) {
			return response()->json([
				'success' => true,
				'date' => $date,
				'extra_ot_report' => $extraOTReport
			]);
		}

		// Export to Excel if requested
		if ($request->input('export') == 'excel') {
			return $this->exportExtraOTToExcel($extraOTReport, $date);
		}

		return view('report.extra_ot_report', [
			'report' => $extraOTReport,
			'date' => $date
		]);
	}

	/**
	 * Test shift detection for a given punch time
	 */
	public function testShiftDetection(Request $request)
	{
		$punchTime = $request->input('punch_time');
		
		if (!$punchTime) {
			return response()->json(['error' => 'punch_time is required'], 400);
		}

		$detectedShift = Attendance::detectShift($punchTime);

		return response()->json([
			'punch_time' => $punchTime,
			'detected_shift' => $detectedShift
		]);
	}

	/**
	 * Calculate attendance summary for an employee for a date range
	 */
	public function getAttendanceSummary(Request $request)
	{
		$employeeId = $request->input('employee_id');
		$startDate = $request->input('start_date');
		$endDate = $request->input('end_date');

		$attendances = Attendance::where('employee_id', $employeeId)
			->whereBetween('attendance_date', [$startDate, $endDate])
			->with(['employee', 'shift'])
			->get();

		$summary = [
			'total_days' => $attendances->count(),
			'total_working_hours' => $attendances->sum('working_hours'),
			'total_overtime_hours' => $attendances->sum('overtime_hours'),
			'total_late_minutes' => $attendances->sum('late_minutes'),
			'total_late_deduction' => $attendances->sum('late_deduction'),
			'total_overtime_amount' => $attendances->sum('overtime_amount'),
			'half_days' => $attendances->where('is_half_day', 1)->count(),
			'on_time_days' => $attendances->where('late_minutes', '<=', 15)->count(),
			'late_days' => $attendances->where('late_minutes', '>', 15)->count()
		];

		return response()->json([
			'employee_id' => $employeeId,
			'period' => $startDate . ' to ' . $endDate,
			'summary' => $summary,
			'attendances' => $attendances
		]);
	}

	private function exportExtraOTToExcel($report, $date)
	{
		// This would need to be implemented with Laravel Excel
		// For now, returning JSON format
		return response()->json([
			'message' => 'Excel export not implemented yet',
			'data' => $report
		]);
	}

	public function bulkUpload()
	{
		$logged_user = auth()->user();
		if ($logged_user->can('create-attendance'))
		{
			return view('timesheet.attendance.bulk_upload');
		}
		return abort(404,__('You are not authorized'));
	}

	public function uploadUserData()
	{
		set_time_limit(300); // 5 minutes timeout

		// Attendance data (excluding 0:00 records)
		$attendanceData = [
			['35', '4/3/2025', '8:21', '17:16'],
			['35', '4/4/2025', '8:09', '17:15'],
			['35', '4/5/2025', '8:13', '17:16'],
			['35', '4/7/2025', '8:10', '17:16'],
			['35', '4/8/2025', '8:06', '17:16'],
			['35', '4/10/2025', '8:18', '17:16'],
			['35', '4/11/2025', '8:14', '17:16'],
			['35', '4/12/2025', '8:14', '17:16'],
			['35', '4/14/2025', '8:16', '17:18'],
			['35', '4/15/2025', '8:14', '17:15'],
			['35', '4/16/2025', '8:18', '17:15'],
			['35', '4/17/2025', '8:15', '17:15'],
			['35', '4/18/2025', '8:13', '17:15'],
			['35', '4/19/2025', '8:14', '17:17'],
			['35', '4/21/2025', '8:15', '17:17'],
			['35', '4/22/2025', '8:13', '17:16'],
			['35', '4/23/2025', '8:15', '17:17'],
			['35', '4/24/2025', '8:12', '17:16'],
			['35', '4/25/2025', '8:11', '17:15'],
			['35', '4/26/2025', '8:24', '17:15'],
			['35', '4/28/2025', '8:10', '17:15'],
			['35', '4/29/2025', '8:13', '17:16'],
			['35', '4/30/2025', '8:11', '19:15'],
			['1', '4/3/2025', '8:10', '17:17'],
			['1', '4/4/2025', '8:10', '17:16'],
			['1', '4/5/2025', '8:11', '17:27'],
			['1', '4/7/2025', '8:14', '17:17'],
			['1', '4/8/2025', '8:14', '17:28'],
			['1', '4/9/2025', '8:12', '17:19'],
			['1', '4/11/2025', '8:08', '17:20'],
			['1', '4/12/2025', '8:12', '17:20'],
			['1', '4/14/2025', '8:10', '17:20'],
			['1', '4/15/2025', '8:09', '17:20'],
			['1', '4/16/2025', '8:13', '17:22'],
			['1', '4/17/2025', '8:13', '17:19'],
			['1', '4/18/2025', '8:13', '17:22'],
			['1', '4/19/2025', '8:13', '17:19'],
			['1', '4/21/2025', '8:14', '17:19'],
			['1', '4/22/2025', '8:09', '17:18'],
			['1', '4/24/2025', '8:08', '17:21'],
			['1', '4/25/2025', '8:07', '17:22'],
			['1', '4/26/2025', '8:09', '17:16'],
			['1', '4/28/2025', '8:12', '17:18'],
			['1', '4/29/2025', '8:11', '17:21'],
			['1', '4/30/2025', '8:10', '17:18'],
			['2', '4/3/2025', '7:54', '17:26'],
			['2', '4/4/2025', '8:09', '17:26'],
			['2', '4/5/2025', '8:04', '17:27'],
			['2', '4/7/2025', '8:11', '17:28'],
			['2', '4/8/2025', '8:08', '17:28'],
			['2', '4/9/2025', '8:10', '17:32'],
			['2', '4/10/2025', '8:02', '17:20'],
			['2', '4/11/2025', '8:08', '12:14'],
			['2', '4/12/2025', '8:12', '17:20'],
			['2', '4/14/2025', '8:08', '17:31'],
			['2', '4/15/2025', '8:10', '17:24'],
			['2', '4/16/2025', '8:08', '17:33'],
			['2', '4/17/2025', '7:57', '17:20'],
			['2', '4/18/2025', '8:08', '17:01'],
			['2', '4/19/2025', '8:09', '17:20'],
			['2', '4/21/2025', '7:55', '17:19'],
			['2', '4/22/2025', '8:09', '17:21'],
			['2', '4/23/2025', '8:09', '17:26'],
			['2', '4/24/2025', '8:08', '17:22'],
			['2', '4/25/2025', '8:07', '16:56'],
			['2', '4/26/2025', '8:12', '17:19'],
			['2', '4/28/2025', '8:07', '17:27'],
			['2', '4/29/2025', '8:11', '16:49'],
			['2', '4/30/2025', '8:04', '17:30']
		];

		$html = '<h2>🚀 Attendance Data Upload</h2>';
		$html .= '<div style="font-family: monospace; background: #f5f5f5; padding: 20px;">';

		// Get staff IDs from the data
		$staffIds = array_unique(array_column($attendanceData, 0));
		$html .= '<p><strong>🔍 Staff IDs in data:</strong> ' . implode(', ', $staffIds) . '</p>';

		// Check which employees exist
		$employees = Employee::whereIn('staff_id', $staffIds)->where('is_active', 1)->get();
		$html .= '<p><strong>👥 Found employees:</strong> ' . $employees->count() . ' / ' . count($staffIds) . '</p>';

		if ($employees->count() == 0) {
			$html .= '<p style="color: red;"><strong>❌ No employees found with these staff IDs!</strong></p>';
			
			// Show available staff IDs
			$availableStaffIds = Employee::where('is_active', 1)->whereNotNull('staff_id')->pluck('staff_id')->take(20);
			$html .= '<p><strong>📋 Available staff IDs in database (first 20):</strong></p>';
			$html .= '<ul>';
			foreach ($availableStaffIds as $id) {
				$html .= "<li>$id</li>";
			}
			$html .= '</ul>';
			
			$html .= '</div>';
			return response($html);
		}

		// Create employee mapping
		$staffToEmployeeId = [];
		$html .= '<div style="margin: 10px 0;">';
		foreach ($employees as $employee) {
			$staffToEmployeeId[$employee->staff_id] = $employee->id;
			$html .= "✓ Staff ID {$employee->staff_id} → {$employee->first_name} {$employee->last_name} (ID: {$employee->id})<br>";
		}
		$html .= '</div>';

		// Process attendance data
		$successCount = 0;
		$skippedCount = 0;
		$errorCount = 0;

		$html .= '<h3>📊 Processing ' . count($attendanceData) . ' attendance records...</h3>';
		$html .= '<div style="max-height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; background: white;">';

		foreach ($attendanceData as $record) {
			[$staffId, $dateStr, $clockIn, $clockOut] = $record;

			try {
				// Check if employee exists
				if (!isset($staffToEmployeeId[$staffId])) {
					$html .= "⚠️ Staff ID $staffId not found in database<br>";
					$errorCount++;
					continue;
				}

				$employeeId = $staffToEmployeeId[$staffId];

				// Convert date format from "4/3/2025" to "2025-04-03"
				$date = \DateTime::createFromFormat('n/j/Y', $dateStr);
				if (!$date) {
					$date = \DateTime::createFromFormat('m/d/Y', $dateStr);
				}

				if (!$date) {
					$html .= "❌ Invalid date format: $dateStr<br>";
					$errorCount++;
					continue;
				}

				$attendanceDate = $date->format('Y-m-d');

				// Check if attendance already exists
				$existingAttendance = Attendance::where('employee_id', $employeeId)
					->where('attendance_date', $attendanceDate)
					->first();

				if ($existingAttendance) {
					$html .= "⚠️ Attendance already exists for staff ID $staffId on $attendanceDate<br>";
					$skippedCount++;
					continue;
				}

				// Format times
				$clockInFormatted = $this->formatTimeForUpload($clockIn);
				$clockOutFormatted = $this->formatTimeForUpload($clockOut);

				// Calculate total work time
				$clockInTime = new \DateTime($clockInFormatted);
				$clockOutTime = new \DateTime($clockOutFormatted);
				$totalWork = $clockInTime->diff($clockOutTime)->format('%H:%I');

				// Create attendance record
				Attendance::create([
					'employee_id' => $employeeId,
					'attendance_date' => $attendanceDate,
					'clock_in' => $clockInTime->format('H:i'),
					'clock_out' => $clockOutTime->format('H:i'),
					'clock_in_ip' => request()->ip(),
					'clock_out_ip' => request()->ip(),
					'clock_in_out' => 0,
					'time_late' => '00:00',
					'early_leaving' => '00:00',
					'overtime' => '00:00',
					'total_work' => $totalWork,
					'total_rest' => '00:00',
					'attendance_status' => 'present'
				]);

				$successCount++;
				$html .= "✅ Added attendance for staff ID $staffId on $attendanceDate ($clockInFormatted - $clockOutFormatted)<br>";

			} catch (\Exception $e) {
				$html .= "❌ Error processing staff ID $staffId on $dateStr: " . $e->getMessage() . "<br>";
				$errorCount++;
			}
		}

		$html .= '</div>';

		$html .= '<h3>=== UPLOAD COMPLETED ===</h3>';
		$html .= "<p style='color: green;'><strong>✅ Successfully uploaded: $successCount records</strong></p>";
		$html .= "<p style='color: orange;'><strong>⚠️ Skipped (duplicates): $skippedCount records</strong></p>";
		$html .= "<p style='color: red;'><strong>❌ Errors: $errorCount records</strong></p>";

		if ($successCount > 0) {
			$html .= '<p style="color: green; font-size: 18px;"><strong>🎉 Attendance data has been successfully uploaded to the database!</strong></p>';
			$html .= '<p><em>💡 Note: Late time, overtime, and other calculations will be processed based on employee shift settings.</em></p>';
		}

		$html .= '</div>';
		return response($html);
	}

	private function formatTimeForUpload($timeStr)
	{
		// Convert "8:21" to "08:21"
		$parts = explode(':', $timeStr);
		if (count($parts) == 2) {
			return sprintf('%02d:%02d', $parts[0], $parts[1]);
		}
		return $timeStr;
	}

	public function debugEmployees()
	{
		$employees = Employee::where('is_active', 1)
			->select('id', 'staff_id', 'first_name', 'last_name')
			->orderBy('id')
			->get();

		$html = '<h3>Employee Debug Information</h3>';
		$html .= '<p><strong>Total Active Employees:</strong> ' . $employees->count() . '</p>';
		
		if ($employees->count() > 0) {
			$html .= '<table border="1" style="border-collapse: collapse; width: 100%;">';
			$html .= '<thead><tr><th>ID</th><th>Staff ID</th><th>Name</th></tr></thead><tbody>';
			
			foreach ($employees as $emp) {
				$html .= '<tr>';
				$html .= '<td>' . $emp->id . '</td>';
				$html .= '<td><strong>' . ($emp->staff_id ?: 'NULL') . '</strong></td>';
				$html .= '<td>' . $emp->first_name . ' ' . $emp->last_name . '</td>';
				$html .= '</tr>';
			}
			$html .= '</tbody></table>';
			
			$html .= '<h4>Sample CSV Format (copy this):</h4>';
			$html .= '<pre>staff_id,attendance_date,clock_in,clock_out<br>';
			
			$sampleStaffIds = $employees->pluck('staff_id')->filter()->take(3);
			foreach ($sampleStaffIds as $index => $staffId) {
				$html .= $staffId . ',2024-01-0' . ($index + 1) . ',08:00,17:00<br>';
			}
			$html .= '</pre>';
		} else {
			$html .= '<p style="color: red;">No active employees found!</p>';
		}
		
		return response($html);
	}

	public function bulkUploadStore(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'employees' => 'required|array|min:1',
			'employees.*' => 'exists:employees,id',
			'start_date' => 'required|date',
			'end_date' => 'required|date|after_or_equal:start_date',
			'clock_in_time' => 'required|date_format:H:i',
			'clock_out_time' => 'required|date_format:H:i|after:clock_in_time'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()->all()]);
		}

		try {
			$employees = Employee::whereIn('id', $request->employees)->with('officeShift')->get();
			$startDate = Carbon::parse($request->start_date);
			$endDate = Carbon::parse($request->end_date);
			$clockInTime = $request->clock_in_time;
			$clockOutTime = $request->clock_out_time;
			
			$attendanceRecords = [];
			$totalRecords = 0;

			foreach ($employees as $employee) {
				$currentDate = $startDate->copy();
				
				while ($currentDate->lte($endDate)) {
					// Skip weekends if requested
					if ($request->skip_weekends && $currentDate->isWeekend()) {
						$currentDate->addDay();
						continue;
					}

					// Check if attendance already exists
					$existingAttendance = Attendance::where('employee_id', $employee->id)
						->where('attendance_date', $currentDate->format('Y-m-d'))
						->first();

					if (!$existingAttendance) {
						// Calculate attendance metrics
						$clockIn = new DateTime($clockInTime);
						$clockOut = new DateTime($clockOutTime);
						
						$attendanceDate = $currentDate->format('Y-m-d');
						$day = strtolower($currentDate->format('l')) . '_in';
						
						// Get shift times
						$shiftIn = null;
						$shiftOut = null;
						if ($employee->officeShift) {
							$shiftInField = strtolower($currentDate->format('l')) . '_in';
							$shiftOutField = strtolower($currentDate->format('l')) . '_out';
							$shiftIn = $employee->officeShift->$shiftInField ? new DateTime($employee->officeShift->$shiftInField) : null;
							$shiftOut = $employee->officeShift->$shiftOutField ? new DateTime($employee->officeShift->$shiftOutField) : null;
						}

						// Calculate lateness
						$timeLate = '00:00';
						if ($shiftIn && $clockIn > $shiftIn) {
							$timeLate = $shiftIn->diff($clockIn)->format('%H:%I');
						}

						// Calculate early leaving
						$earlyLeaving = '00:00';
						if ($shiftOut && $clockOut < $shiftOut) {
							$earlyLeaving = $shiftOut->diff($clockOut)->format('%H:%I');
						}

						// Calculate total work
						$totalWork = $clockIn->diff($clockOut)->format('%H:%I');

						// Calculate overtime
						$overtime = '00:00';
						if ($shiftIn && $shiftOut) {
							$totalWorkDt = new DateTime($totalWork);
							$dutyTime = new DateTime($shiftIn->diff($shiftOut)->format('%H:%I'));
							if ($totalWorkDt > $dutyTime) {
								$overtime = $totalWorkDt->diff($dutyTime)->format('%H:%I');
							}
						}

						$attendanceRecords[] = [
							'employee_id' => $employee->id,
							'attendance_date' => $attendanceDate,
							'clock_in' => $clockIn->format('H:i'),
							'clock_out' => $clockOut->format('H:i'),
							'clock_in_ip' => $request->ip(),
							'clock_out_ip' => $request->ip(),
							'clock_in_out' => 0,
							'time_late' => $timeLate,
							'early_leaving' => $earlyLeaving,
							'overtime' => $overtime,
							'total_work' => $totalWork,
							'total_rest' => '00:00',
							'attendance_status' => 'present'
						];
						$totalRecords++;
					}

					$currentDate->addDay();
				}
			}

			// Insert records in chunks
			if (!empty($attendanceRecords)) {
				$chunks = array_chunk($attendanceRecords, 100);
				foreach ($chunks as $chunk) {
					Attendance::insert($chunk);
				}
			}

			return response()->json([
				'success' => true,
				'message' => "Successfully created {$totalRecords} attendance records for " . count($employees) . " employees.",
				'records_created' => $totalRecords
			]);

		} catch (\Exception $e) {
			return response()->json(['errors' => ['Error: ' . $e->getMessage()]]);
		}
	}

	/**
	 * Calculate late time with 15-minute grace period
	 * @param DateTime $shiftStart
	 * @param DateTime $clockIn
	 * @return string formatted as HH:MM
	 */
	private function calculateLateTimeWithGracePeriod($shiftStart, $clockIn)
	{
		if ($clockIn <= $shiftStart) {
			return '00:00'; // Not late
		}

		// Calculate total late minutes
		$diff = $shiftStart->diff($clockIn);
		$lateMinutes = ($diff->h * 60) + $diff->i;

		// Apply 15-minute grace period rule
		if ($lateMinutes <= 15) {
			return '00:00'; // Within grace period
		}

		// Return actual late time (full time, not minus grace period)
		$hours = floor($lateMinutes / 60);
		$minutes = $lateMinutes % 60;
		return sprintf('%02d:%02d', $hours, $minutes);
	}

	/**
	 * Calculate overtime with proper late deduction and grace period
	 * @param DateTime $shiftEnd
	 * @param DateTime $clockOut
	 * @param DateTime $shiftStart
	 * @param DateTime $clockIn
	 * @return string formatted as HH:MM
	 */
	private function calculateOvertimeWithLateDeduction($shiftEnd, $clockOut, $shiftStart, $clockIn)
	{
		// Calculate gross overtime (time worked beyond shift end)
		if ($clockOut <= $shiftEnd) {
			return '00:00'; // No overtime
		}

		$overtimeDiff = $shiftEnd->diff($clockOut);
		$overtimeMinutes = ($overtimeDiff->h * 60) + $overtimeDiff->i;

		// Calculate late minutes for deduction
		$lateMinutes = 0;
		if ($clockIn > $shiftStart) {
			$lateDiff = $shiftStart->diff($clockIn);
			$rawLateMinutes = ($lateDiff->h * 60) + $lateDiff->i;

			// Only deduct late time if beyond 15-minute grace period
			if ($rawLateMinutes > 15) {
				$lateMinutes = $rawLateMinutes;
			}
		}

		// Calculate net overtime (overtime minus late deduction)
		$netOvertimeMinutes = max(0, $overtimeMinutes - $lateMinutes);

		$hours = floor($netOvertimeMinutes / 60);
		$minutes = $netOvertimeMinutes % 60;
		return sprintf('%02d:%02d', $hours, $minutes);
	}

}
