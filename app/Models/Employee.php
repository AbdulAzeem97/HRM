<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Employee extends Model
{
	use Notifiable;
	protected $fillable = [
		'id','first_name','last_name','staff_id','email','contact_no','date_of_birth','gender','status_id','office_shift_id','salary_id','location_id','designation_id', 'company_id', 'department_id','is_active','is_labor_employee','overtime_allowed','required_hours_per_day',
		'role_users_id','permission_role_id','joining_date','exit_date','marital_status','address','city','nic','country','nic_expiry','cv','skype_id','fb_id',
		'twitter_id','linkedIn_id','blogger_id','basic_salary','payslip_type','leave_id','attendance_id','performance_id','award_id','transfer_id','resignation_id',
		'travel_id','promotion_id','complain_id','warning_id','termination_id','attendance_type','total_leave','remaining_leave','pension_type','pension_amount'];


	public function getFullNameAttribute() {
		try {
			$firstName = $this->first_name ?? '';
			$lastName = $this->last_name ?? '';
			return ucfirst($firstName) . ' ' . ucfirst($lastName);
		} catch (Exception $e) {
			return 'Unknown User';
		}
	}

	public function getBirthDateAttribute() {
		return $this->date_of_birth;
	}

	public function department(){
		return $this->hasOne('App\Models\department','id','department_id');
	}

	public function officeShift(){
		return $this->hasOne('App\Models\office_shift','id','office_shift_id');
	}

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function designation(){
		return $this->hasOne('App\Models\designation','id','designation_id');
	}

	public function status(){
		return $this->hasOne('App\Models\status','id','status_id');
	}

	public function user(){
		return $this->hasOne('App\Models\User','id','id');
	}

	public function role(){
		return $this->hasOne('Spatie\Permission\Models\Role','id','role_users_id');
	}

	public function salaryBasic(){
		return $this->hasMany(SalaryBasic::class);
	}

	public function allowances(){
		return $this->hasMany(SalaryAllowance::class);
	}
	public function deductions(){
		return $this->hasMany(SalaryDeduction::class);
	}
	public function commissions(){
		return $this->hasMany(SalaryCommission::class);
	}
	public function loans(){
		return $this->hasMany(SalaryLoan::class);
	}
	public function otherPayments(){
		return $this->hasMany(SalaryOtherPayment::class);
	}
	public function overtimes(){
		return $this->hasMany(SalaryOvertime::class);
	}
	public function payslips(){
		return $this->hasMany(Payslip::class);
	}

	public function payslipNew(){
		return $this->hasOne(Payslip::class);
	}

	public function employeeAttendance(){
		return $this->hasMany(Attendance::class);
	}

	public function overtimeCalculations(){
		return $this->hasMany(OvertimeCalculation::class);
	}

	public function employeeLeave(){
		return $this->hasMany(leave::class)
			->select('id','start_date','end_date','status','employee_id','leave_type_id','total_days')
			->whereStatus('approved');
	}
	public function employeeLeaveTypeDetail(){
		return $this->hasOne(EmployeeLeaveTypeDetail::class);
	}



	public function setDateOfBirthAttribute($value)
	{
		// Handle empty values explicitly
		if (is_null($value) || $value === '' || $value === '0000-00-00' || $value === 'null' || trim($value) === '' || $value === '00-00-0000') {
			$this->attributes['date_of_birth'] = null;
			return;
		}

		if (class_exists('App\Helpers\DateHelper')) {
			$parsed = DateHelper::parseToddmmyyyy($value);
			// Only set if DateHelper returns a valid date
			if ($parsed && $parsed !== '00-00-0000' && $parsed !== '0000-00-00') {
				$this->attributes['date_of_birth'] = $parsed;
			} else {
				$this->attributes['date_of_birth'] = null;
			}
		} else {
			// Fallback for when DateHelper is not available
			try {
				$date = Carbon::parse($value);
				$this->attributes['date_of_birth'] = $date->format('d-m-Y');
			} catch (Exception $e) {
				$this->attributes['date_of_birth'] = null;
			}
		}
	}

	public function getDateOfBirthAttribute($value)
	{
		try {
			// Handle invalid date values
			if (is_null($value) || $value === '' || $value === '00-00-0000' || $value === '0000-00-00') {
				return '';
			}
			// Return as-is since it's already in dd-mm-yyyy format
			return $value;
		} catch (Exception $e) {
			return '';
		}
	}

	public function setJoiningDateAttribute($value)
	{
		// Handle empty values explicitly
		if (is_null($value) || $value === '' || $value === '0000-00-00' || $value === 'null' || trim($value) === '' || $value === '00-00-0000') {
			$this->attributes['joining_date'] = null;
			return;
		}

		if (class_exists('App\Helpers\DateHelper')) {
			$parsed = DateHelper::parseToddmmyyyy($value);
			// Only set if DateHelper returns a valid date
			if ($parsed && $parsed !== '00-00-0000' && $parsed !== '0000-00-00') {
				$this->attributes['joining_date'] = $parsed;
			} else {
				$this->attributes['joining_date'] = null;
			}
		} else {
			// Fallback for when DateHelper is not available
			try {
				$date = Carbon::parse($value);
				$this->attributes['joining_date'] = $date->format('d-m-Y');
			} catch (Exception $e) {
				$this->attributes['joining_date'] = null;
			}
		}
	}

	public function getJoiningDateAttribute($value)
	{
		try {
			// Handle invalid date values
			if (is_null($value) || $value === '' || $value === '00-00-0000' || $value === '0000-00-00') {
				return '';
			}
			// Return as-is since it's already in dd-mm-yyyy format
			return $value;
		} catch (Exception $e) {
			return '';
		}
	}

	public function setExitDateAttribute($value)
	{
		// Handle empty values explicitly
		if (is_null($value) || $value === '' || $value === '0000-00-00' || $value === 'null' || trim($value) === '' || $value === '00-00-0000') {
			$this->attributes['exit_date'] = null;
			return;
		}

		if (class_exists('App\Helpers\DateHelper')) {
			$parsed = DateHelper::parseToddmmyyyy($value);
			// Only set if DateHelper returns a valid date
			if ($parsed && $parsed !== '00-00-0000' && $parsed !== '0000-00-00') {
				$this->attributes['exit_date'] = $parsed;
			} else {
				$this->attributes['exit_date'] = null;
			}
		} else {
			// Fallback for when DateHelper is not available
			try {
				$date = Carbon::parse($value);
				$this->attributes['exit_date'] = $date->format('d-m-Y');
			} catch (Exception $e) {
				$this->attributes['exit_date'] = null;
			}
		}
	}

	public function getExitDateAttribute($value)
	{
		try {
			// Handle invalid date values
			if (is_null($value) || $value === '' || $value === '00-00-0000' || $value === '0000-00-00') {
				return '';
			}
			// Return as-is since it's already in dd-mm-yyyy format
			return $value;
		} catch (Exception $e) {
			return '';
		}
	}

	public function setNicExpiryAttribute($value)
	{
		// Handle empty values explicitly
		if (is_null($value) || $value === '' || $value === '0000-00-00' || $value === 'null' || trim($value) === '' || $value === '00-00-0000') {
			$this->attributes['nic_expiry'] = null;
			return;
		}

		if (class_exists('App\Helpers\DateHelper')) {
			$parsed = DateHelper::parseToddmmyyyy($value);
			// Only set if DateHelper returns a valid date
			if ($parsed && $parsed !== '00-00-0000' && $parsed !== '0000-00-00') {
				$this->attributes['nic_expiry'] = $parsed;
			} else {
				$this->attributes['nic_expiry'] = null;
			}
		} else {
			// Fallback for when DateHelper is not available
			try {
				$date = Carbon::parse($value);
				$this->attributes['nic_expiry'] = $date->format('d-m-Y');
			} catch (Exception $e) {
				$this->attributes['nic_expiry'] = null;
			}
		}
	}

	public function getNicExpiryAttribute($value)
	{
		try {
			// Handle invalid date values
			if (is_null($value) || $value === '' || $value === '00-00-0000' || $value === '0000-00-00') {
				return '';
			}
			// Return as-is since it's already in dd-mm-yyyy format
			return $value;
		} catch (Exception $e) {
			return '';
		}
	}

	// Helper methods for labor employee management
	public function scopeLaborEmployees($query)
	{
		return $query->where('is_labor_employee', true)->where('is_active', true);
	}

	public function scopeRegularEmployees($query)
	{
		return $query->where('is_labor_employee', false)->where('is_active', true);
	}

	public function isLaborEmployee()
	{
		return $this->is_labor_employee == true;
	}

	public static function markAsLaborEmployee($employeeIds)
	{
		if (!is_array($employeeIds)) {
			$employeeIds = [$employeeIds];
		}

		return self::whereIn('id', $employeeIds)->update(['is_labor_employee' => true]);
	}

	public static function unmarkAsLaborEmployee($employeeIds)
	{
		if (!is_array($employeeIds)) {
			$employeeIds = [$employeeIds];
		}

		return self::whereIn('id', $employeeIds)->update(['is_labor_employee' => false]);
	}

	public static function getAllLaborEmployees($companyId = null)
	{
		$query = self::laborEmployees()->with(['department', 'designation', 'officeShift']);
		
		if ($companyId) {
			$query->where('company_id', $companyId);
		}

		return $query->get();
	}

}
