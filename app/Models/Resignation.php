<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
	protected $fillable = [
		'description', 'company_id','department_id','employee_id','resignation_date','notice_date'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function department(){
		return $this->hasOne('App\Models\department','id','department_id');
	}

	public function employee(){
		return $this->hasOne('App\Models\Employee','id','employee_id');
	}

	public function setResignationDateAttribute($value)
	{
		$this->attributes['resignation_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getResignationDateAttribute($value)
	{
		return $value;
	}

	public function setNoticeDateAttribute($value)
	{
		$this->attributes['notice_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getNoticeDateAttribute($value)
	{
		return $value;
	}
}
