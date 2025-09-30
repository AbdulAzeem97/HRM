<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\Employee;
use App\Models\EmployeeLeaveTypeDetail;
use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Http\traits\MonthlyWorkedHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PayslipController extends Controller {

	use MonthlyWorkedHours;

	public function index(Request $request)
	{
		$logged_user = auth()->user();
        $companies = company::all();
        // Handle date format conversion for month filtering
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

        \Log::info('Payment History: Date conversion', [
            'original_input' => $request->filter_month_year ?? 'empty',
            'converted_date' => $selected_date
        ]);

		if ($logged_user->can('view-payslip'))
		{
			if (request()->ajax())
			{
                if (!empty($request->filter_employee))
                {
                    $payslips = Payslip::with(['employee:id,first_name,last_name,company_id,department_id','employee.company','employee.department'])
                            ->where('employee_id', $request->filter_employee)
                            ->where('month_year', $selected_date)
                            ->get();
                }
                elseif (!empty($request->filter_company)) {
                    $payslips = Payslip::with(['employee:id,first_name,last_name,company_id,department_id','employee.company','employee.department'])
                            ->where('company_id', $request->filter_company)
                            ->where('month_year', $selected_date)
                            ->get();
                }
                else {
                    $payslips = Payslip::with( ['employee:id,first_name,last_name,company_id,department_id','employee.company','employee.department'])
                            ->where('month_year',$selected_date)
                            ->get();
                }

				// return datatables()->of(Payslip::with( ['employee:id,first_name,last_name,company_id,department_id','employee.company','employee.department'])->latest('created_at'))
				return datatables()->of($payslips)
					->setRowId(function ($payslip)
					{
						return $payslip->id;
					})
					->addColumn('bank_account', function ($row)
					{
						return $row->employeeBankAccount->account_number ?? '';
					})
					->addColumn('employee_name', function ($row)
					{
						$employee_name  = "<span><a class='d-block text-bold' style='color:#24ABF2;'>".$row->employee->full_name."</a></span>";
						$company_name = $row->employee->company->company_name ?? '';
						$department_name = $row->employee->department->department_name ?? '';

						return $employee_name.'</br><b>Comapny :</b> '.$company_name.'</br><b>Department :</b> '.$department_name;
					})
					->addColumn('salary_details', function ($row)
					{
						//basic salary
						// $basic_salary = $row->basic_salary;

						//-----------basic salary---------
						if($row->payment_type == 'Hourly') { //Basic Salary (Total)
							$employee = Employee::with('user:id,username','company:id,company_name','department:id,department_name','designation:id,designation_name')
									->select('id','first_name','last_name','joining_date','contact_no','company_id','department_id','designation_id', 'payslip_type')
									->where('id',$row->employee_id)->first();
							$total_minutes = 0 ;
							$total_hours = $row->hours_worked;
							sscanf($total_hours, '%d:%d', $hour, $min);
							//converting in minute
							$total_minutes += $hour * 60 + $min;
							$amount_hours = ($row->basic_salary / 60 ) * $total_minutes;
						}
						else {
							$basic_salary = $row->basic_salary;
						}

						//----------- basic salary ----------


						//allowances
						if($row->allowances){
							$allowance_total = 0;
							foreach($row->allowances as $allowance)
							{
								$allowance_total += $allowance['allowance_amount'];
							}
						}
						else{
							$allowance_total = 0;
						}

						//Total Salary
						// $total_salary = $basic_salary + $allowance_total;

						//Total Salary
						if($row->payment_type == 'Hourly') {
							$total_salary = $amount_hours + $allowance_total;
						}else {
							$total_salary = $basic_salary + $allowance_total;
						}


						//Commision
						if($row->commissions){
							$commission_total = 0;
							foreach($row->commissions as $commission)
							{
								$commission_total += $commission['commission_amount'];
							}
						}
						//Loan
						if($row->loans){
							$loan_total=0;
							foreach($row->loans as $loan)
							{
								$loan_total += (float) $loan['monthly_payable'];
							}
						}
						//Other Payments
						if($row->other_payments){
							$other_payment_total = 0;
							foreach($row->other_payments as $other_payment)
							{
								$other_payment_total += $other_payment['other_payment_amount'];
							}
						}
						//Overtime
						if($row->overtimes){
							$overtime_total = 0;
							foreach($row->overtimes as $overtime)
							{
								$overtime_total += $overtime['overtime_amount'];
							}
						}
						//Deduction
						if($row->deductions){
							$deduction_total = 0.00;
							foreach($row->deductions as $deduction)
							{
								$deduction_total += $deduction['deduction_amount'];
							}
						}

						//Basic Salary(Total)
						if($row->payment_type == 'Hourly') {
							$data = "<div class='d-flex'>
									<div class='ml-auto'> Basic Salary (Total) :</div>
									<div class='ml-auto'>".$amount_hours."</div>
								</div>";

						} //Basic Salary
						else {
							$data = "<div class='d-flex'>
									<div class='ml-auto'> Basic Salary :</div>
									<div class='ml-auto'>".$basic_salary."</div>
								</div>";
						}

						if ($row->allowances) {
							$data .= "<div class='d-flex'>
									<div class='ml-auto'>(+) Allowance:</div>
									<div class='ml-auto'>".$allowance_total."</div>
								</div>";
						}
						//Total Salary
						$data .= "<div class='d-flex'>
									<div class='ml-auto'> <b>Total Salary</b> :</div>
									<div class='ml-auto'><b>".config('variable.currency')." ".$total_salary."</b></div>
								</div>";

						if($row->commissions) {
							$data .= "<div class='d-flex'>
									<div class='ml-auto'>(+) Commision:</div>
									<div class='ml-auto'>".$commission_total."</div>
								</div>";
						}
						if($row->other_payments){
							$data .= "<div class='d-flex'>
										<div class='ml-auto'> Other Payment &nbsp:</div>
										<div class='ml-auto'>".$other_payment_total."</div>
									</div>";
						}
						if($row->overtimes){
							$data .= "<div class='d-flex'>
										<div class='ml-auto'>(+) Overtime &nbsp:</div>
										<div class='ml-auto'>".$overtime_total."</div>
									</div>";
						}
						if($row->loans){
							$data .= "<div class='d-flex'>
									<div class='ml-auto'>(-) Loan:</div>
									<div class='ml-auto'>".$loan_total."</div>
								</div>";
						}
						if($row->deductions){
							$data .= "<div class='d-flex'>
										<div class='ml-auto'>(-) Deduction &nbsp:</div>
										<div class='ml-auto'>".$deduction_total."</div>
									</div>";
						}
						//Net Payable
						$data .= "<div class='d-flex'>
									<div class='ml-auto'><b>Net Payable</b> :</div>
									<div class='ml-auto'><b>".config('variable.currency')." ".$row->net_salary."</b></div>
								</div>";

						return $data;
					})
					->addColumn('action', function ($data)
					{
							$button  = '<a id="' . $data->payslip_key . '" class="show btn btn-primary btn-sm" href="' . route('payslip_details.show', $data->payslip_key) . '"><i class="dripicons-preview"></i></a>';
							$button .= '&nbsp;&nbsp;';
							$button .= '<a id="' . $data->payslip_key . '" class="download btn btn-info btn-sm" href="' . route('payslip.pdf', $data->payslip_key) . '"><i class="dripicons-download"></i></a>';
							return $button;
					})
					->rawColumns(['action','employee_name','salary_details'])
					->make(true);
			}

			return view('salary.payslip.index',compact('companies', 'selected_date'));
		}

		return abort('403', __('You are not authorized'));
	}

	public function show(Payslip $payslip)
	{
		$employee = Employee::with('user:id,username','company:id,company_name','department:id,department_name','designation:id,designation_name')
			->select('id','first_name','last_name','joining_date','contact_no','company_id','department_id','designation_id', 'payslip_type','pension_type','pension_amount')
			->where('id',$payslip->employee_id)
			->first();

        // Employee Leave Type Details
        $employeeLeaveTypeDetail = EmployeeLeaveTypeDetail::where('employee_id',$payslip->employee_id)->first();
        $leaveTypeUnserialize = [];
        if ($employeeLeaveTypeDetail) {
            $leaveTypeUnserialize = unserialize($employeeLeaveTypeDetail->leave_type_detail);
        }

		$total_minutes = 0;
		$total_hours = $payslip->hours_worked; //correction
		sscanf($total_hours, '%d:%d', $hour, $min);
		//converting in minute
		$total_minutes += $hour * 60 + $min;
		$amount_hours = ($payslip->basic_salary / 60 ) * $total_minutes;

		return view('salary.payslip.show',compact('payslip','employee','total_hours','amount_hours','leaveTypeUnserialize'));
	}

	public function delete(Payslip $payslip){
		if ($payslip->exists)
		{
			$payslip->delete();

			return response()->json(['success' => __('Payslip Deleted successfully')]);
		}
		return response()->json(['error' => 'Operation Unsuccessful']);
	}


	public function printPdf(Payslip $payslip)
	{
		$month_year = $payslip->month_year;
		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date  = date('Y-m-d', strtotime('last day of ' . $month_year));

		$employee = Employee::with(['user:id,username','company.Location.country',
			'department:id,department_name','designation:id,designation_name',
			'employeeAttendance' => function ($query) use ($first_date, $last_date){
				$query->whereBetween('attendance_date', [$first_date, $last_date]);
			}])
			->select('id','first_name','last_name','joining_date','contact_no','company_id','department_id','designation_id','payslip_type','pension_amount')
			->where('id',$payslip->employee_id)->first()->toArray();


		// return $payslip->pension_amount;

		$total_minutes = 0 ;
		$total_hours = $payslip->hours_worked; //correction
		sscanf($total_hours, '%d:%d', $hour, $min);
		//converting in minute
		$total_minutes += $hour * 60 + $min;
		$amount_hours = ($payslip->basic_salary / 60 ) * $total_minutes;
		$employee['hours_amount'] = $amount_hours;
        $employee['pension_amount'] = $payslip->pension_amount;

		// Prepare variables for PDF view - Handle both JSON strings and arrays
		$overtimes_data = is_string($payslip->overtimes) ? json_decode($payslip->overtimes, true) ?: [] : (is_array($payslip->overtimes) ? $payslip->overtimes : []);


		$pdfData = array_merge($payslip->toArray(), $employee, [
			'hours_amount' => $amount_hours,
			'allowances' => is_string($payslip->allowances) ? json_decode($payslip->allowances, true) ?: [] : (is_array($payslip->allowances) ? $payslip->allowances : []),
			'commissions' => is_string($payslip->commissions) ? json_decode($payslip->commissions, true) ?: [] : (is_array($payslip->commissions) ? $payslip->commissions : []),
			'loans' => is_string($payslip->loans) ? json_decode($payslip->loans, true) ?: [] : (is_array($payslip->loans) ? $payslip->loans : []),
			'deductions' => is_string($payslip->deductions) ? json_decode($payslip->deductions, true) ?: [] : (is_array($payslip->deductions) ? $payslip->deductions : []),
			'other_payments' => is_string($payslip->other_payments) ? json_decode($payslip->other_payments, true) ?: [] : (is_array($payslip->other_payments) ? $payslip->other_payments : []),
			'overtimes' => $overtimes_data,
		]);

		PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','tempDir'=>storage_path('temp')]);
        $pdf = PDF::loadView('salary.payslip.pdf', $pdfData);
        return $pdf->stream();
	}
}

