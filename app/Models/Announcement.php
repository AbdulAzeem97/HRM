<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
	protected $fillable = [
		'title','start_date','end_date','summary','description','is_notify','company_id','department_id', 'is_active','added_by'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function department(){
		return $this->hasOne('App\Models\department','id','department_id');
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
