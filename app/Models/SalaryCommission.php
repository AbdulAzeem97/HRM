<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;

class SalaryCommission extends Model
{
	protected $guarded=[];

	public function employee(){
		return $this->hasOne('App\Models\Employee','id','employee_id');
	}

	public function setFirstDateAttribute($value)
	{
		$this->attributes['first_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getFirstDateAttribute($value)
	{
		if($value === null)
		{
			return '';
		}
		else{
			// Return as-is since it's already in dd-mm-yyyy format
			return $value;
		}
	}

}
