<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SalaryLoan extends Model
{
	protected $guarded=[];

	protected $casts = [
		'loan_amount' => 'float',
		'amount_remaining' => 'float',
		'monthly_payable' => 'float',
		'loan_time' => 'integer',
		'time_remaining' => 'integer',
	];

	public function employee(){
		return $this->hasOne('App\Models\Employee','id','employee_id');
	}

	public function setStartDateAttribute($value)
	{
		$this->attributes['start_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getStartDateAttribute($value)
	{
		return $value;
	}

	public function setEndDateAttribute($value)
	{
		$this->attributes['end_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getEndDateAttribute($value)
	{
		return $value;
	}

}
